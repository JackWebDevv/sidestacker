<?php
require_once 'includes/db_connect.php';

try {
    $sql = file_get_contents(__DIR__ . '/sql/fix_saved_items.sql');
    $pdo->exec($sql);
    echo "Saved items table recreated successfully\n";
} catch (PDOException $e) {
    echo "Error updating saved items table: " . $e->getMessage() . "\n";
}
