<?php
require_once '../includes/db_connect.php';

try {
    // Add premium column to users table
    $pdo->exec("ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS is_premium BOOLEAN DEFAULT FALSE,
        ADD COLUMN IF NOT EXISTS premium_until DATE NULL");
    echo "Users table updated with premium columns\n";

    // Create advertisements table
    $pdo->exec("CREATE TABLE IF NOT EXISTS advertisements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        ad_type ENUM('banner', 'sidebar', 'sponsored') NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        url VARCHAR(255),
        image_url VARCHAR(255),
        start_date DATE,
        end_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "Advertisements table created successfully\n";

    // Create business_plans table
    $pdo->exec("CREATE TABLE IF NOT EXISTS business_plans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        industry VARCHAR(100),
        template_path VARCHAR(255),
        is_premium BOOLEAN DEFAULT FALSE,
        downloads INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Business plans table created successfully\n";

    // Create jobs table
    $pdo->exec("CREATE TABLE IF NOT EXISTS jobs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        company VARCHAR(255) NOT NULL,
        description TEXT,
        requirements TEXT,
        location VARCHAR(255),
        salary_range VARCHAR(100),
        job_type ENUM('full-time', 'part-time', 'contract', 'freelance') NOT NULL,
        status ENUM('active', 'filled', 'expired') DEFAULT 'active',
        user_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "Jobs table created successfully\n";

    // Create content table
    $pdo->exec("CREATE TABLE IF NOT EXISTS content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        content TEXT,
        content_type VARCHAR(50) NOT NULL,
        user_id INT,
        status ENUM('draft', 'published') DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "Content table created successfully\n";

    // Create products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Products table created successfully\n";

    // Create tools table
    $pdo->exec("CREATE TABLE IF NOT EXISTS tools (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        category VARCHAR(50) NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Tools table created successfully\n";

    // Add any missing columns to users table
    $pdo->exec("ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS role ENUM('admin', 'user') DEFAULT 'user',
        ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') DEFAULT 'active',
        ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL");
    echo "Users table updated successfully\n";

} catch (PDOException $e) {
    die("Error setting up tables: " . $e->getMessage());
}

echo "All tables have been set up successfully!\n";
?>
