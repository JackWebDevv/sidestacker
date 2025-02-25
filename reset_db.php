<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';
$password = '';

try {
    // First connect without database selected
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop database if exists
    $pdo->exec("DROP DATABASE IF EXISTS sidestacker_db");
    echo "Database dropped successfully.<br>";
    
    // Create database
    $pdo->exec("CREATE DATABASE sidestacker_db");
    echo "Database created successfully.<br>";
    
    echo "Please refresh your index page to reinitialize the database with test data.";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
