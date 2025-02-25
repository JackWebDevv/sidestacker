<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS sidestacker_db");
    $pdo->exec("USE sidestacker_db");
    
    // Drop existing tables
    $pdo->exec("DROP TABLE IF EXISTS content");
    $pdo->exec("DROP TABLE IF EXISTS users");
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        status ENUM('active', 'inactive') DEFAULT 'active'
    )");
    echo "Users table created successfully\n";

    // Create content table
    $pdo->exec("CREATE TABLE IF NOT EXISTS content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content_type ENUM('blog', 'tutorial') NOT NULL,
        content_text LONGTEXT NOT NULL,
        excerpt TEXT,
        author VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        status ENUM('draft', 'published') DEFAULT 'draft',
        featured BOOLEAN DEFAULT FALSE,
        slug VARCHAR(255) UNIQUE,
        meta_description TEXT,
        tags VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "Content table created successfully\n";

    // Insert default admin user
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->execute(['admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin']);
    echo "Admin user created\n";

    // Get the admin user's ID
    $stmt = $pdo->query('SELECT id FROM users WHERE username = "admin" LIMIT 1');
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin_id = $user['id'];

    // Insert test content
    $content = [
        [$admin_id, 'Getting Started with Dropshipping', 'blog', 'This is a comprehensive guide to starting your dropshipping business...', 'Learn how to start your dropshipping business from scratch with this comprehensive guide.', 'admin', 'published', 1, 'getting-started-dropshipping'],
        [$admin_id, 'SEO Best Practices 2025', 'blog', 'Learn the latest SEO techniques and strategies...', 'Stay ahead of the competition with these cutting-edge SEO strategies.', 'admin', 'published', 0, 'seo-best-practices-2025'],
        [$admin_id, 'Email Marketing Guide', 'tutorial', 'Step by step guide to email marketing...', 'Master email marketing with our detailed tutorial.', 'admin', 'published', 1, 'email-marketing-guide']
    ];

    $stmt = $pdo->prepare('INSERT INTO content (user_id, title, content_type, content_text, excerpt, author, status, featured, slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    
    foreach ($content as $item) {
        $stmt->execute($item);
        echo "Inserted: {$item[1]}\n";
    }

    echo "All done! Database recreated with test content.";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
