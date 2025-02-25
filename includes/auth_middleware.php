<?php
require_once 'session_manager.php';

function requireLogin() {
    $sessionManager = SessionManager::getInstance();
    
    if (!$sessionManager->isLoggedIn()) {
        $sessionManager->setFlashMessage('error', 'Please login to access this page');
        header("Location: /login.php");
        exit();
    }
    
    return $sessionManager;
}

function requireAdmin() {
    $sessionManager = requireLogin();
    
    if ($sessionManager->getRole() !== 'admin') {
        $sessionManager->setFlashMessage('error', 'Access denied. Admin privileges required.');
        header("Location: /dashboard.php");
        exit();
    }
    
    return $sessionManager;
}
