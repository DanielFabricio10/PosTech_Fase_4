<?php

define('DSN', 'mysql:host=my_sql');
define('USER', 'root');
define('PASS', 'root');

try {

    $connectionDB = new PDO(DSN, USER, PASS);
    $connectionDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $connectionDB->exec("CREATE DATABASE IF NOT EXISTS PosTech");
    $connectionDB->exec("USE PosTech");
    
    $sqlTabelas = "
        CREATE TABLE IF NOT EXISTS Client (
            cpfcnpj VARCHAR(14) PRIMARY KEY,
            name VARCHAR(60) NOT NULL,
            lastname VARCHAR(60) NOT NULL,
            phone VARCHAR(30) NOT NULL,
            email VARCHAR(60) NOT NULL,
            birthdate DATE NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS Address (
            ID INT PRIMARY KEY AUTO_INCREMENT,
            cpfcnpj VARCHAR(14),
            zipcode VARCHAR(15) NOT NULL,
            street VARCHAR(200) NOT NULL,
            number VARCHAR(20),
            neighborhood VARCHAR(60),
            city VARCHAR(60) NOT NULL,
            uf CHAR(2) NOT NULL,
            FOREIGN KEY (cpfcnpj) REFERENCES Client(cpfcnpj)
        );
        
        CREATE TABLE IF NOT EXISTS `Order` (
            idpedido INT PRIMARY KEY AUTO_INCREMENT,
            cpfcnpj VARCHAR(14) NOT NULL,
            statuspedido VARCHAR(25) NOT NULL,
            FOREIGN KEY (cpfcnpj) REFERENCES Client(cpfcnpj)
        );
        
        CREATE TABLE IF NOT EXISTS Product(
            ID INT PRIMARY KEY AUTO_INCREMENT,
            nameproduct VARCHAR(60) NOT NULL,
            description VARCHAR(60) NOT NULL,
            category VARCHAR(30) NOT NULL,
            reference VARCHAR(60) NOT NULL,
            price VARCHAR(60),
            quantity INTEGER NOT NULL,
            datacriacao DATE NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS OrderProduct(
            ID INT PRIMARY KEY AUTO_INCREMENT,
            idpedido INT NOT NULL,
            reference VARCHAR(60) NOT NULL,
            price VARCHAR(60) NOT NULL,
            quantity INTEGER NOT NULL,
            FOREIGN KEY (idpedido) REFERENCES  `Order`(idpedido)
        );
        
        CREATE TABLE IF NOT EXISTS QueueProducts(
            ID INT PRIMARY KEY AUTO_INCREMENT,
            date timestamp NOT NULL,
            idpedido VARCHAR(60) NOT NULL,
            status varchar(60) NOT NULL
        );
    ";

    $connectionDB->exec($sqlTabelas);
    
} catch (PDOException $e) {
    exit('Erro conexão banco de dados: '.$e->getMessage());
}