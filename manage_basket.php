<?php
require_once 'includes/db_connect.php';
require_once 'includes/auth_middleware.php';

header('Content-Type: application/json');

try {
    // Require login
    $sessionManager = requireLogin();
    $userId = $sessionManager->getUserId();

    // Get parameters
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if (!$productId || !in_array($action, ['add', 'update', 'remove'])) {
        throw new Exception('Invalid request parameters');
    }

    // Check if product exists and is active
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if (!$product) {
        throw new Exception('Product not found');
    }

    if ($action === 'add' || $action === 'update') {
        if ($quantity < 1) {
            throw new Exception('Quantity must be at least 1');
        }

        // Try to update existing basket item
        $stmt = $pdo->prepare("INSERT INTO basket_items (user_id, product_id, quantity) 
                              VALUES (?, ?, ?)
                              ON DUPLICATE KEY UPDATE quantity = ?");
        $stmt->execute([$userId, $productId, $quantity, $quantity]);
        
        echo json_encode([
            'success' => true, 
            'message' => $action === 'add' ? 'Product added to basket' : 'Basket updated'
        ]);
    } else {
        // Remove from basket
        $stmt = $pdo->prepare("DELETE FROM basket_items WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        echo json_encode(['success' => true, 'message' => 'Product removed from basket']);
    }

    // Get updated basket count
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM basket_items WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    $basketCount = $result['total'] ?? 0;

    // Include basket count in response
    echo json_encode([
        'success' => true,
        'basketCount' => (int)$basketCount
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
