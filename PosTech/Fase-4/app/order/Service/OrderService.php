<?php

namespace App\Service\Order;

use App\Model\Order\OrderModel;

class OrderService {

    private $DB;

    function __construct($DB) {
        $this->DB = $DB;
    }

    public function insertOrder(Object $body) {
        
        $stmt = $this->DB->prepare('SELECT cpfcnpj FROM Client WHERE cpfcnpj = :cpfCnpj;');
        $stmt->bindParam(':cpfCnpj', $body->cpf);
        $stmt->execute();

        if(count($stmt->fetchAll()) == 0) {
            throw new \Exception('Não existe cliente cadastrado com este cpf');
        }

        $stmt = $this->DB->prepare('INSERT INTO `Order` (cpfcnpj, statuspedido) VALUES (:cpfcnpj, :statuspedido)');

        $statusPedido = 'pendente';

        $stmt->bindParam(':cpfcnpj', $body->cpf);
        $stmt->bindParam(':statuspedido', $statusPedido);

        $stmt->execute();

        $rowCount = $stmt->rowCount();

        if($rowCount < 1) {
            throw new \Exception('Erro ao incluir cliente e status do pedido');
        }

        $idOrder = $this->DB->lastInsertId();

        $stmt2 = $this->DB->prepare('INSERT INTO OrderProduct (idpedido, reference, price, quantity) VALUES (:idpedido, :reference, :price, :quantity)');

        foreach($body->products as $itens) {

            $stmt = $this->DB->prepare('SELECT reference, price FROM Product WHERE reference = :reference;');
            $stmt->bindParam(':reference', $itens->reference);
            $stmt->execute();

            $result = $stmt->fetch($this->DB::FETCH_ASSOC);

            if(empty($result['reference'])) {
                throw new \Exception('Não existe produto cadastrado com esta referencia '.$itens->reference);
            }

            $stmt2->bindParam(':idpedido', $idOrder);
            $stmt2->bindParam(':reference', $itens->reference);
            $stmt2->bindParam(':price', $result['price']);
            $stmt2->bindParam(':quantity',  $itens->quantity);

            if(!$stmt2->execute()) {
                throw new \Exception('Erro ao inserir produto no pedido');
            }
        }

        return (Object)[
            'statusHttp' => 200, 
            'json' => json_encode([
                'status' => 'success',
                'message' => 'Order '.$idOrder.' cadastrado com sucesso'
            ])
        ];
    }

    public function updateOrder($body) {
        
        $stmt = $this->DB->prepare('SELECT idpedido FROM `Order` o WHERE  idpedido = :idpedido;');
        $stmt->bindParam(':idpedido', $body->orderCode);
        $stmt->execute();

        if(count($stmt->fetchAll()) == 0) {
            throw new \Exception('Não existe pedido cadastrado com este id');
        }

        $stmt = $this->DB->prepare("UPDATE `Order`
        SET statuspedido = :statuspedido
        WHERE idpedido = :idpedido;");

        $stmt->bindParam(':idpedido', $body->orderCode);
        $stmt->bindParam(':statuspedido', $body->status);
        
        if(!$stmt->execute()) {
            throw new \Exception('Erro ao atualizar o pedido');
        }

        return (Object)[
            'statusHttp' => 200, 
            'json' => json_encode([
                'status' => 'success',
                'message' => 'Order '.$idOrder.' atualizado para '.$body->status.' com sucesso'
            ])
        ];
    }

    public function consultOrder($orderId) {
       
        if($orderId > 0) {

            $stmt3 = $this->DB->prepare("SELECT * FROM `Order` WHERE idpedido = :idpedido");
            $stmt3->bindParam(':idpedido', $orderId);
            $stmt3->execute();
            $result3 = $stmt3->fetch($this->DB::FETCH_ASSOC);

            if(empty(intval($result3['idpedido']))) {
                throw new \Exception('Pedido não encontrado');
            } 

            return (Object) [
                'statusHttp' => 200, 
                'json' => json_encode($result3)
            ];

        } else {
            
            $stmt3 = $this->DB->prepare("SELECT * FROM `Order`");            
            $stmt3->execute();
            $result3 = $stmt3->fetchAll($this->DB::FETCH_ASSOC);
           
            if(empty($result3[0]['idpedido'])) {
                throw new \Exception('Sem pedidos');
            } 

            return (Object) [
                'statusHttp' => 200, 
                'json' => json_encode($result3)
            ];
        }
    }

}