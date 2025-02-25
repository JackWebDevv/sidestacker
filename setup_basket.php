<?php
require_once 'includes/db_connect.php';

try {
    $sql = file_get_contents(__DIR__ . '/sql/create_basket.sql');
    $pdo->exec($sql);
    echo "Basket table created successfully\n";
} catch (PDOException $e) {
    echo "Error creating basket table: " . $e->getMessage() . "\n";
}
