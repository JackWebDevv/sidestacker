-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample products
INSERT INTO products (name, description, price, slug, status, featured) VALUES
('Digital Marketing Guide', 'Comprehensive guide to modern digital marketing strategies and techniques.', 29.99, 'digital-marketing-guide', 'active', 1),
('SEO Mastery Course', 'Learn advanced SEO techniques to rank higher in search engines.', 49.99, 'seo-mastery-course', 'active', 1),
('Social Media Toolkit', 'Complete toolkit for managing and growing your social media presence.', 39.99, 'social-media-toolkit', 'active', 1),
('Content Creation Bundle', 'Premium bundle of content creation tools and templates.', 59.99, 'content-creation-bundle', 'active', 1),
('Email Marketing Templates', 'Professional email marketing templates for various campaigns.', 19.99, 'email-marketing-templates', 'active', 0),
('Analytics Dashboard Pro', 'Advanced analytics dashboard for tracking marketing metrics.', 79.99, 'analytics-dashboard-pro', 'active', 0);
