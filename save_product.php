<?php
require_once 'includes/db_connect.php';
require_once 'includes/auth_middleware.php';

header('Content-Type: application/json');

try {
    // Require login
    $sessionManager = requireLogin();
    $userId = $sessionManager->getUserId();

    // Get product ID from POST request
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if (!$productId || !in_array($action, ['save', 'unsave'])) {
        throw new Exception('Invalid request parameters');
    }

    // Check if product exists
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    if (!$stmt->fetch()) {
        throw new Exception('Product not found');
    }

    if ($action === 'save') {
        try {
            // Try to insert the saved item
            $stmt = $pdo->prepare("INSERT INTO saved_items (user_id, item_id, item_type) VALUES (?, ?, 'product')");
            $stmt->execute([$userId, $productId]);
            echo json_encode(['success' => true, 'message' => 'Product saved successfully']);
        } catch (PDOException $e) {
            // If duplicate entry, return success
            if ($e->getCode() == 23000) {
                echo json_encode(['success' => true, 'message' => 'Product is already saved']);
            } else {
                throw $e;
            }
        }
    } else {
        // Unsave the product
        $stmt = $pdo->prepare("DELETE FROM saved_items WHERE user_id = ? AND item_id = ? AND item_type = 'product'");
        $stmt->execute([$userId, $productId]);
        echo json_encode(['success' => true, 'message' => 'Product removed from saved items']);
    }
} catch (PDOException $e) {
    error_log('Database error in save_product.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'A database error occurred. Please try again.'
    ]);
} catch (Exception $e) {
    error_log('Error in save_product.php: ' . $e->getMessage());
    error_log('User ID: ' . ($userId ?? 'not set'));
    error_log('Product ID: ' . ($productId ?? 'not set'));
    error_log('Action: ' . ($action ?? 'not set'));
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
