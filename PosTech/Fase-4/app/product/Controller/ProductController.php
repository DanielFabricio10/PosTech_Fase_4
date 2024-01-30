<?php

namespace App\Controller\Product;

use App\Service\Product\ProductService;

class ProductController {

    private $productService;

    function __construct($DB) {
        $this->productService = new ProductService($DB);
    }

    public function insertProduct($body) {
        
        if(!is_object($body)) {
            $body = json_decode($body);
        }

        if(!$body) {
            throw new \Exception('Body informado é invalido');
        }

        return $this->productService->insertProduct($body);
    }

    public function updateProduct($body, $reference) {

        if(!is_object($body)) {
            $body = json_decode($body);
        }

        if(!$body) {
            throw new \Exception('Body informado é invalido');
        }

        return $this->productService->updateProduct($body, $reference);
    }

    public function deleteProduct($reference) {
        return $this->productService->deleteProduct($reference);
    }

    public function searchProduct($reference) {
        return $this->productService->searchProduct($reference);
    }

    public function searchProductCategory($category) {
        return $this->productService->searchProductCategory($category);
    }


}