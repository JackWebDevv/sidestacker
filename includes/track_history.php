<?php

function track_user_history($user_id, $item_type, $item_id, $action_type) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO user_history (user_id, item_type, item_id, action_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $item_type, $item_id, $action_type]);
    } catch (PDOException $e) {
        // Silently fail - we don't want to interrupt the user experience if history tracking fails
        error_log("Error tracking user history: " . $e->getMessage());
    }
}
