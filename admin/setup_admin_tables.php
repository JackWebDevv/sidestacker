<?php
require_once(__DIR__ . '/../includes/db_connect.php');
require_once(__DIR__ . '/verify_admin.php');

function executeSQLFile($pdo, $filename) {
    $sql = file_get_contents($filename);
    $queries = explode(';', $sql);
    
    $success = true;
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query)) continue;
        
        try {
            $pdo->exec($query);
        } catch (PDOException $e) {
            echo "Error executing query: " . $e->getMessage() . "\n";
            echo "Query: " . $query . "\n\n";
            $success = false;
        }
    }
    return $success;
}

try {
    if (executeSQLFile($pdo, __DIR__ . '/sql/admin_tables.sql')) {
        echo "Successfully created admin tables!";
    } else {
        echo "There were some errors while creating the tables. Please check the output above.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
