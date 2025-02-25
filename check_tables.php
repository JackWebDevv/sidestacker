<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db_connect.php';

try {
    // Check users table
    echo "<h3>Users Table:</h3>";
    $stmt = $pdo->query("SELECT * FROM users");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";

    // Check content table
    echo "<h3>Content Table:</h3>";
    $stmt = $pdo->query("SELECT * FROM content");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";

    // Check products table
    echo "<h3>Products Table:</h3>";
    $stmt = $pdo->query("SELECT * FROM products");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";

    // Check tools table
    echo "<h3>Tools Table:</h3>";
    $stmt = $pdo->query("SELECT * FROM tools");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
