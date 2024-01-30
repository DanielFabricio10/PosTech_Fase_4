<?php

ini_set('display_errors', 'Off');

$auth = base64_encode($_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW']);

if(empty($auth) || $auth != 'cG9zdGVjaDp0ZXN0ZQ==') {
    exit(http_response_code(403));
}

if(strtoupper($_SERVER['REQUEST_METHOD']) != 'GET') {
    exit(http_response_code(405));
}

require '../product/Model/Product.php';
require '../database/DataBaseConnection.php';
require '../product/Service/ProductService.php';
require '../product/Controller/ProductController.php';

$category = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : '';

use App\Controller\Product\ProductController;

try {

    $prodClass = new ProductController($connectionDB);
    $retorno = $prodClass->searchProductCategory($category);

    http_response_code($retorno->statusHttp);
    header('Content-Type: application/json');
    echo $retorno->json;

} catch(Exception $error) {
    http_response_code(400);
    echo $error->getMessage();
}