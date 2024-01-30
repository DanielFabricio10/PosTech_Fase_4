<?php

namespace App\Service\Client;

use App\Model\Client\Client;
use App\Model\Client\Address;

class ClientService {

    private $DB;

    function __construct($DB) {
        $this->DB = $DB;
    }

    private function validateCPF($cpf) {
        
        if(empty($cpf)) {
            return false;
        }

        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }
        
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
    
        return true;
    }

    private function verifyZipCode($zipCode) {
        
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'viacep.com.br/ws/'.$zipCode.'/json/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $response = json_decode($response);

        curl_close($curl);

        if(!in_array($httpCode, [200,201,202,204])) {
            return false;
        }

        return $response;
    }

    private function buildClient(Object $client) {
       
        $validateCpf = $this->validateCPF($client->cpf);

        if($validateCpf !== true) {
            throw new \Exception('Cpf informado para o cliente não é valido');
        } 
            
        $clientClass = new Client();
        $clientClass->cpf = $client->cpf;
        
        if(empty($client->name) || empty($client->lastName)) {
            throw new \Exception('Nome e sobre nome do cliente precisam ser informados');
        } else {
            $clientClass->name = $client->name;
            $clientClass->lastName = $client->lastName;
        }

        if(empty($client->phone)) {
            $clientClass->phone = '99999999';
        } else {
            $clientClass->phone = preg_replace('/[^0-9]/', '', $client->phone);
        }

        $clientClass->dateOfBirth = date('Y-m-d', strtotime($client->birthDate));

        if(!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $client->email)) {
            $clientClass->email = $client->name.'_'.$client->lastName.'@postech.com';
        } else {
            $clientClass->email = $client->email;
        }

        return $clientClass;
    }

    private function buildAddress(Object $address) {
    
        $zipcodeValidade = $this->verifyZipCode($address->zipCode);

        if($zipcodeValidade === false) {
            throw new \Exception('Cep informado não é um cep valido');
        } 
        
        $addressClass = new Address();
        $addressClass->zipCode = preg_replace('/[^0-9]/', '', $address->zipCode);
        
        if(empty($address->street)) {
            throw new \Exception('Endereço não informado');
        } else {
            $addressClass->street = $address->street;
        }

        $addressClass->number = !empty($address->number) ? $address->number : 's/n';
        $addressClass->uf = !empty($address->uf) ? $address->uf : $zipcodeValidade->uf;
        $addressClass->city = !empty($address->city) ? $address->city : $zipcodeValidade->localidade;
        $addressClass->neighborhood = !empty($address->neighborhood) ? $address->neighborhood : 'Centro';
        
        return $addressClass;
    }

    public function insertClient(Object $client) {
        
        $clientData  = $this->buildClient($client->client);
        $addressData = $this->buildAddress($client->client->address);

        $stmt = $this->DB->prepare('SELECT cpfcnpj FROM Client WHERE cpfcnpj = :cpfCnpj;');
        $stmt->bindParam(':cpfCnpj', $clientData->cpf);
        $stmt->execute();

        if(count($stmt->fetchAll()) > 0) {
            throw new \Exception('Cliente já está cadastrado');
        }

        $stmt = $this->DB->prepare('INSERT INTO Client (cpfcnpj, name, lastname, phone, email, birthdate) VALUES (:cpfcnpj, :name, :lastname, :phone, :email, :birthdate);');

        $stmt->bindParam(':cpfcnpj', $clientData->cpf);
        $stmt->bindParam(':name', $clientData->name);
        $stmt->bindParam(':lastname', $clientData->lastName);
        $stmt->bindParam(':phone', $clientData->phone);
        $stmt->bindParam(':email', $clientData->email);
        $stmt->bindParam(':birthdate', $clientData->dateOfBirth);

        $stmt->execute();

        $stmt2 = $this->DB->prepare('INSERT INTO Address (cpfcnpj, street, number, zipcode, neighborhood, city, uf) VALUES (:cpfcnpj, :street, :number, :zipcode, :neighborhood, :city, :uf)');
        $stmt2->bindParam(':cpfcnpj',  $clientData->cpf);
        $stmt2->bindParam(':street', $addressData->street);
        $stmt2->bindParam(':number', $addressData->number);
        $stmt2->bindParam(':zipcode',  $addressData->zipCode);
        $stmt2->bindParam(':neighborhood', $addressData->neighborhood);
        $stmt2->bindParam(':city', $addressData->city);
        $stmt2->bindParam(':uf',  $addressData->uf);

        $stmt2->execute();

        return (Object)[
            'statusHttp' => 200, 
            'json' => json_encode([
                'status' => 'success',
                'message' => 'Cliente cadastrado com sucesso'
            ])
        ];
    }

    public function consultClient($cpfCnpj) {
       
        $validateCpf = $this->validateCPF($cpfCnpj);

        if($validateCpf !== true) {
            throw new \Exception('Cpf informado para a consulta não é valido');
        }

        $stmt = $this->DB->prepare("SELECT * FROM Client WHERE cpfcnpj = :cpfcnpj");

        $stmt->bindParam(':cpfcnpj', $cpfCnpj);
        $stmt->execute();

        $result = $stmt->fetch($this->DB::FETCH_ASSOC);

        if(!isset($result['cpfcnpj'])) {
            throw new \Exception('Cliente não encontrado');
        }

        return (Object)[
            'statusHttp' => 200, 
            'json' => json_encode($result)
        ];
    }
}