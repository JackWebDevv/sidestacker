<?php
require_once '../includes/db_connect.php';
require_once '../includes/session_manager.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Check if user is logged in
$sessionManager = SessionManager::getInstance();
if (!$sessionManager->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get POST data
$raw_data = file_get_contents('php://input');
error_log("Raw POST data: " . $raw_data);

$data = json_decode($raw_data, true);
error_log("Decoded data: " . print_r($data, true));

$item_type = $data['item_type'] ?? '';
$item_id = $data['item_id'] ?? 0;

error_log("Processing save request - Type: $item_type, ID: $item_id, User ID: " . $sessionManager->getUserId());

if (!in_array($item_type, ['content', 'tool']) || !$item_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

try {
    // Check if item exists in saved_items
    $check_stmt = $pdo->prepare("SELECT id FROM saved_items WHERE user_id = ? AND item_type = ? AND item_id = ?");
    $check_stmt->execute([$sessionManager->getUserId(), $item_type, $item_id]);
    $existing = $check_stmt->fetch();

    if ($existing) {
        // Remove from saved items
        $delete_stmt = $pdo->prepare("DELETE FROM saved_items WHERE id = ?");
        $delete_stmt->execute([$existing['id']]);
        error_log("Item unsaved successfully - ID: " . $existing['id']);
        echo json_encode(['status' => 'unsaved']);
    } else {
        // Add to saved items
        $insert_stmt = $pdo->prepare("INSERT INTO saved_items (user_id, item_type, item_id) VALUES (?, ?, ?)");
        $insert_stmt->execute([$sessionManager->getUserId(), $item_type, $item_id]);
        error_log("Item saved successfully - Last Insert ID: " . $pdo->lastInsertId());
        echo json_encode(['status' => 'saved']);
    }
} catch (PDOException $e) {
    error_log("Error in toggle_save.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
