<?php
require_once '../includes/db_connect.php';

// Create settings table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

try {
    $pdo->exec($sql);
    echo "Settings table created successfully!";
} catch (PDOException $e) {
    echo "Error creating settings table: " . $e->getMessage();
}
?>
