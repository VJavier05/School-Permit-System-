<?php
// Database configuration
$host = 'localhost';      
$dbname = 'school_system'; 
$username = 'root'; 
$password = ''; 
$port = '3307';

try {

    // DSN CALLING THE SET VARIABLE FOR DB
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname"; 

    // CREATING INSTANCE USING THE DSN
    $pdo = new PDO($dsn, $username, $password);
    
    // CATCH ERROR IN CONNECTING DB
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // OUTPUT
    // echo "Connected successfully to the database!";
} catch (PDOException $e) {
    // If there is an error, display it
    echo "Connection failed: " . $e->getMessage();
}
?>