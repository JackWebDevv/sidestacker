<?php
require_once 'includes/db_connect.php';
require_once 'includes/auth_middleware.php';
require_once 'includes/session_manager.php';

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get session manager and ensure user is logged in
$sessionManager = requireLogin();
$userId = $sessionManager->getUserId();

// Get tool ID from POST data
$toolId = filter_input(INPUT_POST, 'tool_id', FILTER_VALIDATE_INT);
if (!$toolId) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid tool ID']);
    exit;
}

try {
    // Check if tool exists
    $tool_stmt = $pdo->prepare("SELECT id FROM tools WHERE id = ?");
    $tool_stmt->execute([$toolId]);
    if (!$tool_stmt->fetch()) {
        throw new Exception('Tool not found');
    }

    // Check if already saved
    $check_stmt = $pdo->prepare("SELECT id FROM saved_items WHERE user_id = ? AND item_type = 'tool' AND item_id = ?");
    $check_stmt->execute([$userId, $toolId]);
    if ($check_stmt->fetch()) {
        // If already saved, remove it (toggle functionality)
        $delete_stmt = $pdo->prepare("DELETE FROM saved_items WHERE user_id = ? AND item_type = 'tool' AND item_id = ?");
        $delete_stmt->execute([$userId, $toolId]);
        echo json_encode(['status' => 'unsaved']);
    } else {
        // Save the tool
        $save_stmt = $pdo->prepare("INSERT INTO saved_items (user_id, item_type, item_id, created_at) VALUES (?, 'tool', ?, NOW())");
        $save_stmt->execute([$userId, $toolId]);
        echo json_encode(['status' => 'saved']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
