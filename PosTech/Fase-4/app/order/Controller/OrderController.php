<?php

namespace App\Controller\Order;

use App\Service\Order\OrderService;

class OrderController {

    private $orderService;

    function __construct($DB) {
        $this->orderService = new OrderService($DB);
    }

    public function insertOrder($body) {
        
        if(!is_object($body)) {
            $body = json_decode($body);
        }

        if(!$body) {
            throw new \Exception('Body informado é invalido');
        }

        return $this->orderService->insertOrder($body);
    }

    public function updateOrder($body) {
        return $this->orderService->updateOrder($body);
    }

    public function consultOrder($orderId) {
        return $this->orderService->consultOrder($orderId);
    }
}