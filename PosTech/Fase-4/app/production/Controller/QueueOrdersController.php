<?php

namespace App\Controller\QueueOrders;

use App\Service\QueueOrders\QueueOrdersService;

class QueueOrdersController {

    private $queueOrdersService;

    function __construct($DB, $mongo) {
        $this->queueOrdersService = new QueueOrdersService($DB, $mongo);
    }

    public function insertOrderQueue($orderId) {
        return $this->queueOrdersService->insertOrderQueue($orderId);      
    }

    public function consultQueue($orderId = 0, $status = '') {

        if($orderId > 0) {
            return $this->queueOrdersService->consultQueue(['numeroPedido' => $orderId]);
        } else if(!empty($status)) {
            return $this->queueOrdersService->consultQueue(['statusPedido' => $status]);
        } else {
            return $this->queueOrdersService->consultQueue([]);
        }
    }

}