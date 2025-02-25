<?php
require_once 'includes/db_connect.php';
require_once 'includes/auth_middleware.php';

header('Content-Type: application/json');

try {
    $sessionManager = SessionManager::getInstance();
    $isLoggedIn = $sessionManager->isLoggedIn();
    $basketCount = 0;

    if ($isLoggedIn) {
        $userId = $sessionManager->getUserId();
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM basket_items WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        $basketCount = (int)($result['total'] ?? 0);
    }

    echo json_encode([
        'success' => true,
        'basketCount' => $basketCount
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching basket count'
    ]);
}
