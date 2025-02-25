<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../includes/db_connect.php';

try {
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        // Admin exists, verify they have admin role
        if ($admin['role'] !== 'admin') {
            throw new Exception("Access denied: User does not have admin privileges");
        }
    } else {
        // Create admin user if it doesn't exist
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, status) VALUES ('admin', ?, 'admin', 'active')");
        $stmt->execute([$password]);
    }
} catch (Exception $e) {
    die("Admin verification failed: " . $e->getMessage());
}
?>
