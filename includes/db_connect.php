<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'sidestacker_db';
$username = 'root';
$password = '';

try {
    // First connect without database selected
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("DROP DATABASE IF EXISTS $dbname");
    $pdo->exec("CREATE DATABASE $dbname");
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create users table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        is_premium BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        status ENUM('active', 'inactive') DEFAULT 'active'
    )");

    // Create content table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
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
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    // Create tools table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS tools (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        category VARCHAR(100) NOT NULL,
        type ENUM('free', 'premium') NOT NULL,
        icon_class VARCHAR(100) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        sort_order INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        status ENUM('active', 'inactive') DEFAULT 'active'
    )");

    // Create products table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        status ENUM('active', 'inactive') DEFAULT 'active',
        featured BOOLEAN DEFAULT FALSE,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Create basket_items table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS basket_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    // Check if we need to insert test data
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();

    if ($userCount == 0) {
        // Insert admin user
        $pdo->exec("INSERT INTO users (username, email, password, role) 
                    VALUES ('admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')");

        // Insert test content
        $pdo->exec("INSERT INTO content (user_id, title, content_type, content_text, excerpt, author, status, featured, slug)
                    VALUES 
                    (1, 'Getting Started with Dropshipping', 'blog', 'This is a comprehensive guide to starting your dropshipping business...', 'Learn how to start your dropshipping business from scratch with this comprehensive guide.', 'admin', 'published', 1, 'getting-started-dropshipping'),
                    (1, 'SEO Best Practices 2025', 'blog', 'Learn the latest SEO techniques and strategies...', 'Stay ahead of the competition with these cutting-edge SEO strategies.', 'admin', 'published', 1, 'seo-best-practices-2025'),
                    (1, 'Email Marketing Guide', 'tutorial', 'Step by step guide to email marketing...', 'Master email marketing with our detailed tutorial.', 'admin', 'published', 1, 'email-marketing-guide')");

        // Insert test tools
        $pdo->exec("INSERT INTO tools (name, description, category, type, icon_class, slug, sort_order, status)
                    VALUES 
                    ('Keyword Research Tool', 'Find the best keywords for your content', 'SEO', 'free', 'fas fa-search', 'keyword-research', 1, 'active'),
                    ('Backlink Analyzer', 'Analyze your backlink profile', 'SEO', 'premium', 'fas fa-link', 'backlink-analyzer', 2, 'active'),
                    ('Social Media Scheduler', 'Schedule your social media posts', 'Social Media', 'premium', 'fas fa-clock', 'social-scheduler', 3, 'active'),
                    ('Email Template Builder', 'Create beautiful email templates', 'Email Marketing', 'free', 'fas fa-envelope', 'email-builder', 4, 'active')");

        // Insert test products
        $pdo->exec("INSERT INTO products (name, description, price, slug, status, featured, image_url)
                    VALUES 
                    ('Digital Marketing Guide', 'Comprehensive guide to modern digital marketing strategies and techniques.', 29.99, 'digital-marketing-guide', 'active', 1, 'assets/images/products/digital-marketing-guide.jpg'),
                    ('SEO Mastery Course', 'Learn advanced SEO techniques to rank higher in search engines.', 49.99, 'seo-mastery-course', 'active', 1, 'assets/images/products/seo-mastery-course.jpg'),
                    ('Social Media Toolkit', 'Complete toolkit for managing and growing your social media presence.', 39.99, 'social-media-toolkit', 'active', 1, 'assets/images/products/social-media-toolkit.jpg'),
                    ('Content Creation Bundle', 'Premium bundle of content creation tools and templates.', 59.99, 'content-creation-bundle', 'active', 1, 'assets/images/products/content-creation-bundle.jpg'),
                    ('Email Marketing Templates', 'Professional email marketing templates for various campaigns.', 19.99, 'email-marketing-templates', 'active', 0, 'assets/images/products/email-marketing-templates.jpg'),
                    ('Analytics Dashboard Pro', 'Advanced analytics dashboard for tracking marketing metrics.', 79.99, 'analytics-dashboard-pro', 'active', 0, 'assets/images/products/analytics-dashboard-pro.jpg')");
    }

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
