<?php
    $host= "localhost";
    $name= "login";
    $user = "appuser";          
    $password = "StrongPassword123!";

    try{
        $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }catch(PDOException $e){
        echo"Error: " .$e->getMessage();
        die();
    }
    
?>