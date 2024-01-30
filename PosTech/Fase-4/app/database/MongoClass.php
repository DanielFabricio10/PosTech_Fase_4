<?php

class MongoClass {

    private $database;
    private $collection;

    private $mainConnecction;
    private $recordConnection;
    private $consultConnection;

    function __construct() {
        
        $this->setMainConnection();
        $this->setRecordConnection();
        $this->migrationMongoDb('admin', 'FilaPedidos');
    }


    private function setDatabase($database) {
        $this->database = $database;
    }

    private function setCollection($collection) {
        $this->collection = $collection;
    }

    private function setMainConnection() {
        try {
    
            $this->mainConnecction = new MongoDB\Driver\Manager("mongodb://mongo-container:27017");
                
        } catch(MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            echo "Erro de timeout de conexão: " . $e->getMessage() . " conexão principal \n";
        } catch(MongoDB\Driver\Exception\Exception $error) {
            echo "Erro ao conectar ao Mongo DB: ". $error->getMessage() . " conexão principal \n";
        }
    }

    private function setRecordConnection() {
        try {
    
            $this->recordConnection = new MongoDB\Driver\BulkWrite;
                
        } catch(MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            echo "Erro de timeout de conexão: " . $e->getMessage() . " conexão de gravação \n";
        } catch(MongoDB\Driver\Exception\Exception $error) {
            echo 'Erro ao conectar ao Mongo DB: '. $error->getMessage() . 'conexão de gravação \n';
        }
    }

    private function setConsultConnection($query = []) {
        try {
    
            $this->consultConnection = new MongoDB\Driver\Query($query);
                
        } catch(MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            echo "Erro de timeout de conexão: " . $e->getMessage() . " conexão de consulta \n";
        } catch(MongoDB\Driver\Exception\Exception $error) {
            echo 'Erro ao conectar ao Mongo DB: '. $error->getMessage() . 'conexão de consulta \n';
        }
    }

    private function migrationMongoDb($dbName, $collectionName) {
        
        $command     = new MongoDB\Driver\Command(['listCollections' => 1]);
        $cursor      = $this->mainConnecction->executeCommand($dbName, $command);
        $collections = current($cursor->toArray());

        $collectionExists = false;

        if(!is_array($collections)) {
            if($collections->name === $collectionName) {
                $collectionExists = true;
            }
        } else {
            foreach($collections as $collection) {
                if($collection->name === $collectionName) {
                    $collectionExists = true;
                    break;
                }
            }
        }

        try {
            $command = new MongoDB\Driver\Command(['create' => $collectionName]);
            $this->mainConnecction->executeCommand($dbName, $command);
        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Erro migration mongo db: " . $e->getMessage() . "\n";
        }
    }

    public function insertDocument($documentData, $database, $collection) {
        
        if(empty($documentData) || empty($database) || empty($collection)) {
            throw new Exception('Não informado dados para inserção');
        }

        $this->setDatabase($database);
        $this->setCollection($collection);

        try {
            
            $this->recordConnection->insert($documentData);
            $result = $this->mainConnecction->executeBulkWrite($this->database.'.'.$this->collection, $this->recordConnection);
        
        } catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
           return (object)['status' => 0, 'message' => $e->getMessage()];
        } catch (MongoDB\Driver\Exception\Exception $e) {
            return (object)['status' => 0, 'message' => $e->getMessage()];
        }

        return (object)['status' => 1, 'message' => $result];
    }

    public function consultDocument($query, $database, $collection) {

        if(!is_array($query) || empty($database) || empty($collection)) {
            throw new Exception('Não informado parametros de consulta');
        }
        
        $this->setDatabase($database);
        $this->setCollection($collection);
        $this->setConsultConnection($query);

        try {
            $retorno = $this->mainConnecction->executeQuery($this->database.'.'.$this->collection, $this->consultConnection);
        } catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            return (object)['status' => 0, 'message' => $e->getMessage()];
        } catch (MongoDB\Driver\Exception\Exception $e) {
             return (object)['status' => 0, 'message' => $e->getMessage()];
        }

        return (object)['status' => 1, 'result' => $retorno];
    }
}