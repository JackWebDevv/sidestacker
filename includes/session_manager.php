<?php

class SessionManager {
    private static $instance = null;
    private $sessionTimeout = 7200; // 2 hours in seconds
    
    private function __construct() {
        // Set secure session parameters before starting the session
        if (session_status() === PHP_SESSION_NONE) {
            // Configure session settings
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            
            // Now start the session
            session_start();
        }
        
        // Check session validity
        $this->validateSession();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new SessionManager();
        }
        return self::$instance;
    }
    
    private function validateSession() {
        if (isset($_SESSION['LAST_ACTIVITY'])) {
            // Check if session has expired
            if (time() - $_SESSION['LAST_ACTIVITY'] > $this->sessionTimeout) {
                $this->destroySession();
                return false;
            }
        }
        
        // Update last activity time
        $_SESSION['LAST_ACTIVITY'] = time();
        
        // Regenerate session ID periodically to prevent session fixation
        if (!isset($_SESSION['CREATED'])) {
            $_SESSION['CREATED'] = time();
        } else if (time() - $_SESSION['CREATED'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['CREATED'] = time();
        }
        
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && $this->validateSession();
    }
    
    public function getUserId() {
        return $this->isLoggedIn() ? $_SESSION['user_id'] : null;
    }
    
    public function getUsername() {
        return $this->isLoggedIn() ? $_SESSION['username'] : null;
    }
    
    public function getRole() {
        return $this->isLoggedIn() ? $_SESSION['role'] : null;
    }
    
    public function login($userId, $username, $role) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['CREATED'] = time();
        $_SESSION['LAST_ACTIVITY'] = time();
        session_regenerate_id(true);
    }
    
    public function destroySession() {
        $_SESSION = array();
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
    }
    
    public function setFlashMessage($type, $message) {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    public function getFlashMessage() {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        return null;
    }
}

// Function to get the SessionManager instance
function getSessionManager() {
    return SessionManager::getInstance();
}
