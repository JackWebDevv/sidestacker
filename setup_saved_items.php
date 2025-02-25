<?php
require_once 'includes/db_connect.php';

try {
    $sql = file_get_contents(__DIR__ . '/sql/create_saved_items.sql');
    $pdo->exec($sql);
    echo "Saved items table created successfully\n";
} catch (PDOException $e) {
    echo "Error creating saved items table: " . $e->getMessage() . "\n";
}
