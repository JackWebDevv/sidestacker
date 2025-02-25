<?php
require_once '../includes/db_connect.php';

try {
    // Check users table structure
    $stmt = $pdo->query("DESCRIBE users");
    echo "Users table structure:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    // Check if admin user exists
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'admin'");
    echo "\nAdmin users:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
