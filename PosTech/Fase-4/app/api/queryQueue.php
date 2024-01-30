<?php

ini_set('display_errors', 'Off');

$auth = base64_encode($_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW']);

if(empty($auth) || $auth != 'cG9zdGVjaDp0ZXN0ZQ==') {
    exit(http_response_code(403));
}

if(strtoupper($_SERVER['REQUEST_METHOD']) != 'GET') {
    exit(http_response_code(405));
}

require '../database/MongoClass.php';
require '../database/DataBaseConnection.php';
require '../production/Model/QueueOrders.php';
require '../production/Service/QueueOrdersService.php';
require '../production/Controller/QueueOrdersController.php';

$mongoClass = new MongoClass();

$orderId    = isset($_GET['orderId']) && !empty($_GET['orderId']) ? intval($_GET['orderId']) : '';
$status     = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : '';

use App\Controller\QueueOrders\QueueOrdersController;

try {
    $queueClass = new QueueOrdersController($connectionDB, $mongoClass);

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($queueClass->consultQueue($orderId, $status));

} catch(Exception $error) {
    http_response_code(400);
    echo $error->getMessage();
}
