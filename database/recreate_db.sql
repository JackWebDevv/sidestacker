-- Drop existing tables
DROP TABLE IF EXISTS content;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Create content table
CREATE TABLE IF NOT EXISTS content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content_type ENUM('blog', 'tutorial') NOT NULL,
    content_text LONGTEXT NOT NULL,
    excerpt TEXT,
    author VARCHAR(100),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('draft', 'published') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    slug VARCHAR(255) UNIQUE,
    meta_description TEXT,
    tags VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert test content with Lorem Picsum images
INSERT INTO content (user_id, title, content_type, content_text, excerpt, author, status, featured, slug, image_url)
VALUES 
(1, 'Getting Started with Dropshipping', 'blog', 'This is a comprehensive guide to starting your dropshipping business...', 'Learn how to start your dropshipping business from scratch with this comprehensive guide.', 'admin', 'published', 1, 'getting-started-dropshipping', 'https://picsum.photos/800/400?random=1'),
(1, 'SEO Best Practices 2025', 'blog', 'Learn the latest SEO techniques and strategies...', 'Stay ahead of the competition with these cutting-edge SEO strategies.', 'admin', 'published', 0, 'seo-best-practices-2025', 'https://picsum.photos/800/400?random=2'),
(1, 'Email Marketing Guide', 'tutorial', 'Step by step guide to email marketing...', 'Master email marketing with our detailed tutorial.', 'admin', 'published', 1, 'email-marketing-guide', 'https://picsum.photos/800/400?random=3');
