<?php 

    $host = 'localhost';
    $port = 3306;
    $username = "root";
    $password = "root";
    $dbName = "assignment2";
    global $pdo;
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbName", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Failed while connecting to database: ".$e->getMessage());
    }

?>