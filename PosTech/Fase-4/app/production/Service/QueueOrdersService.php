<?php

namespace App\Service\QueueOrders;

use App\Model\Queue\QueueOrders;

class QueueOrdersService {

    private $DB;
    private $mongo;

    function __construct($DB, $mongo) {
        $this->DB = $DB;
        $this->mongo = $mongo;
    }

    public function insertOrderQueue($orderId) {
        
        $document = [
            'numeroPedido' => $orderId,
            'statusPedido' => 'Em Preparacao',
            'dataPedido' => time()
        ];

        return $this->mongo->insertDocument($document,'admin','FilaPedidos');
    }

    public function consultQueue($query) {

        $return = [];
        $result = $this->mongo->consultDocument($query, 'admin', 'FilaPedidos');
       
        foreach($result->result as $document) {
            unset($document->_id);
            $document->dataPedido = date('Y-m-d', $document->dataPedido);
            array_push($return, $document);
        }

        if(count($return) == 0 && !is_array($result->result)) {
            $document = current($result->result->toArray());

            unset($document->_id);

            $dataConvertida = date('Y-m-d', $document->dataPedido);
            $document->dataPedido = $dataConvertida;

            array_push($return, $document);
        } 

        return $return;
    }
}