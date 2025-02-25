<?php
require_once 'includes/db_connect.php';

try {
    $sql = file_get_contents(__DIR__ . '/sql/update_products_table.sql');
    $pdo->exec($sql);
    echo "Products table updated successfully\n";
} catch (PDOException $e) {
    echo "Error updating products table: " . $e->getMessage() . "\n";
}
