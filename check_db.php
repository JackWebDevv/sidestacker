<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db_connect.php';

try {
    // Check products table
    $stmt = $pdo->query("SELECT * FROM products");
    echo "<h3>Products:</h3>";
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";

    // Check content table
    $stmt = $pdo->query("SELECT * FROM content");
    echo "<h3>Content:</h3>";
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";

    // Check tools table
    $stmt = $pdo->query("SELECT * FROM tools");
    echo "<h3>Tools:</h3>";
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
