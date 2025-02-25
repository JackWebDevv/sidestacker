<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'sidestacker_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('SITE_NAME', 'Sidestacker');
define('SITE_URL', 'http://localhost/Sidestacker');
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('ASSETS_URL', SITE_URL . '/assets');

// Session configuration
define('SESSION_NAME', 'sidestacker_session');
define('SESSION_LIFETIME', 86400); // 24 hours

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'name' => SESSION_NAME,
        'cookie_lifetime' => SESSION_LIFETIME,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);
}
