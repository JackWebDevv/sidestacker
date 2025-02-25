<?php
require_once '../includes/db_connect.php';

// Create or update users table with necessary columns
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

try {
    $pdo->exec($sql);
    
    // Check if admin user exists, if not create one
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount == 0) {
        // Create default admin user
        $defaultAdmin = [
            'username' => 'admin',
            'email' => 'admin@sidestacker.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'status' => 'active'
        ];
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $defaultAdmin['username'],
            $defaultAdmin['email'],
            $defaultAdmin['password'],
            $defaultAdmin['role'],
            $defaultAdmin['status']
        ]);
        
        echo "Default admin user created successfully!\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
        echo "Please change these credentials after first login!\n";
    }
    
    echo "Users table updated successfully!";
} catch (PDOException $e) {
    echo "Error updating users table: " . $e->getMessage();
}
?>
