<?php

ini_set('display_errors', 'Off');

$auth = base64_encode($_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW']);

if(empty($auth) || $auth != 'cG9zdGVjaDp0ZXN0ZQ==') {
    exit(http_response_code(403));
}

if(strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
    exit(http_response_code(405));
}

require '../client/Model/Client.php';
require '../client/Model/Address.php';
require '../client/Service/ClientService.php';
require '../database/DataBaseConnection.php';
require '../client/Controller/ClientController.php';

$json = json_decode(file_get_contents('php://input'));

use App\Controller\Client\ClientController;

try {

    $controllerClass = new ClientController($connectionDB);
    $retorno = $controllerClass->registerClient($json);

    http_response_code($retorno->statusHttp);
    header('Content-Type: application/json');
    echo $retorno->json;

} catch(Exception $error) {
    http_response_code(400);
    echo $error->getMessage();
}