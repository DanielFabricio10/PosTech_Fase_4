<?php

namespace App\Service\Product;

use App\Model\Product\ProductModel;

class ProductService {

    private $DB;

    function __construct($DB) {
        $this->DB = $DB;
    }

    private function buildProduct(Object $product) {
        
        $productClass = new ProductModel();

        if(!empty($product->nameProduct) || !is_string($product->nameProduct)) {
            $productClass->name = $product->nameProduct;
        } else {
            throw new \Exception('Titulo do produto não informado');
        } 

        if(!empty($product->description) || !is_string($product->description)) {
            $productClass->description = $product->description;
        } else {
            throw new \Exception('Descrição do produto não informado');
        }

        if(!empty($product->reference)) {
            $productClass->reference = $product->reference;
        } else {
            throw new \Exception('O produto não pode ser cadastrado sem referência');
        }

        $productClass->category = !empty($product->category) ? $product->category : 'Sem categoria';
        $productClass->price    = !empty($product->price) ? $product->price : 99999;
        $productClass->quantity    = !empty($product->quantity) ? $product->quantity : 0;

        return $productClass;
    }

    public function insertProduct(Object $product) {
        
        $productData = $this->buildProduct($product);

        $stmt = $this->DB->prepare('SELECT reference FROM Product WHERE reference = :reference;');
        $stmt->bindParam(':reference', $productData->reference);
        $stmt->execute();

        if(count($stmt->fetchAll()) > 0) {
            throw new \Exception('Já existe um produto cadastrado com esta referencia');
        }

        $stmt = $this->DB->prepare('INSERT INTO Product (nameproduct, description, category, reference, price, quantity, datacriacao) VALUES (:nameproduct, :description, :category, :reference, :price, :quantity, :datacriacao)');

        $stmt->bindParam(':nameproduct', $productData->name);
        $stmt->bindParam(':description', $productData->description);
        $stmt->bindParam(':category', $productData->category);
        $stmt->bindParam(':reference', $productData->reference);
        $stmt->bindParam(':price', $productData->price);
        $stmt->bindParam(':quantity', $productData->quantity);
        $stmt->bindParam(':datacriacao', date('Y-m-d'));

        if(!$stmt->execute()) {
            throw new \Exception('Erro ao cadastrar o produto');
        }

        return (Object)[
            'statusHttp' => 200, 
            'json' => json_encode([
                'status' => 'success',
                'message' => 'Produto cadastrado com sucesso'
            ])
        ];
    }

    public function updateProduct($product, $reference) {
        
        $productData = $this->buildProduct($product);

        $stmt = $this->DB->prepare('SELECT reference FROM Product WHERE reference = :reference;');
        $stmt->bindParam(':reference', $reference);
        $stmt->execute();

        if(count($stmt->fetchAll()) == 0) {
            throw new \Exception('Não encontrado produto com esta referencia');
        }

        $stmt = $this->DB->prepare('UPDATE Product SET nameproduct = :nameproduct, description = :description, category = :category, price = :price, quantity = :quantity WHERE reference = :reference');

        $stmt->bindParam(':nameproduct', $productData->name);
        $stmt->bindParam(':description', $productData->description);
        $stmt->bindParam(':category', $productData->category);
        $stmt->bindParam(':reference', $productData->reference);
        $stmt->bindParam(':price', $productData->price);
        $stmt->bindParam(':quantity', $productData->quantity);

        if(!$stmt->execute()) {
            throw new \Exception('Erro ao atualizar produto');
        }

        return (Object)[
            'statusHttp' => 200, 
            'json' => json_encode([
                'status' => 'success',
                'message' => 'Produto atualizado com sucesso'
            ])
        ];
    }

    public function deleteProduct($reference) {
        
        $stmt = $this->DB->prepare('SELECT reference FROM Product WHERE reference = :reference;');
        $stmt->bindParam(':reference', $reference);
        $stmt->execute();

        if(count($stmt->fetchAll()) == 0) {
            throw new \Exception('Não encontrado produto com esta referencia');
        }

        $stmt = $this->DB->prepare('DELETE FROM Product WHERE reference = :reference');
        $stmt->bindParam(':reference', $reference);

        if(!$stmt->execute()) {
            throw new \Exception('Erro ao deletar produto');
        }

        return (Object)[
            'statusHttp' => 200, 
            'json' => json_encode([
                'status' => 'success',
                'message' => 'Produto deletado com sucesso'
            ])
        ];
    }

    public function searchProduct($reference) {
        
        $stmt = $this->DB->prepare("SELECT * FROM Product WHERE reference = :reference");

        $stmt->bindParam(':reference', $reference);
        $stmt->execute();

        $result = $stmt->fetch($this->DB::FETCH_ASSOC);

        if(empty($result)) {
            throw new \Exception('Produto não encontrado');
        }

        return (Object)[
            'statusHttp' => 200, 
            'json' => json_encode($result)
        ];
    }

    public function searchProductCategory($category) {
        
        $stmt = $this->DB->prepare("SELECT * FROM Product WHERE category = :category");

        $stmt->bindParam(':category', $category);
        $stmt->execute();

        $result = $stmt->fetchAll($this->DB::FETCH_ASSOC);
        $jsonFinal = [];

        foreach($result as $result1) {
            array_push($jsonFinal, $result1);
        }

        return (Object)[
            'statusHttp' => 200, 
            'json' => json_encode($jsonFinal)
        ];
    }
}
