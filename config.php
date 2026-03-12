<?php

// Load environment variables
require_once "load-env.php";

    $host= $_ENV['DB_HOST'];
    $name= $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];          
    $password = $_ENV['DB_PASS'];

    try{
        $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }catch(PDOException $e){
        echo"Error: " .$e->getMessage();
        die();
    }
    
?>