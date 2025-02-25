<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $host = 'localhost';
    $dbname = 'sidestacker_db';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database connection successful!\n\n";
    
    // Test users table
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Users in database:\n";
    print_r($users);
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
