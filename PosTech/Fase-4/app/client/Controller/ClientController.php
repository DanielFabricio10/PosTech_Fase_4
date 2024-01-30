<?php

namespace App\Controller\Client;

use App\Service\Client\ClientService;

class ClientController {

    private $clientService;

    function __construct($DB) {
        $this->clientService = new ClientService($DB);
    }

    public function registerClient($body) {
        
        if(!is_object($body)) {
            $body = json_decode($body);
        }

        if(!$body) {
            throw new \Exception('Body informado é invalido');
        }

        return $this->clientService->insertClient($body);
    }

    public function consultClient($cpfCnpj) {
        return $this->clientService->consultClient($cpfCnpj);
    }

}