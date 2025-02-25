USE sidestacker_db;

-- Insert admin user if not exists
INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE id=id;

-- Insert some test content
INSERT INTO content (user_id, title, content_type, content_text, excerpt, author, status, featured, slug)
VALUES 
(1, 'Getting Started with Dropshipping', 'blog', 'This is a comprehensive guide to starting your dropshipping business...', 'Learn how to start your dropshipping business from scratch with this comprehensive guide.', 'admin', 'published', 1, 'getting-started-dropshipping'),
(1, 'SEO Best Practices 2025', 'blog', 'Learn the latest SEO techniques and strategies...', 'Stay ahead of the competition with these cutting-edge SEO strategies.', 'admin', 'published', 0, 'seo-best-practices-2025'),
(1, 'Email Marketing Guide', 'tutorial', 'Step by step guide to email marketing...', 'Master email marketing with our detailed tutorial.', 'admin', 'published', 1, 'email-marketing-guide');

-- Insert test tools
INSERT INTO tools (name, description, category, type, icon_class, slug, sort_order, status)
VALUES 
('Keyword Research Tool', 'Find the best keywords for your content', 'SEO', 'free', 'fas fa-search', 'keyword-research', 1, 'active'),
('Backlink Analyzer', 'Analyze your backlink profile', 'SEO', 'premium', 'fas fa-link', 'backlink-analyzer', 2, 'active'),
('Social Media Scheduler', 'Schedule your social media posts', 'Social Media', 'premium', 'fas fa-clock', 'social-scheduler', 3, 'active'),
('Email Template Builder', 'Create beautiful email templates', 'Email Marketing', 'free', 'fas fa-envelope', 'email-builder', 4, 'active');

-- Insert test products
INSERT INTO products (name, description, price, slug, status, featured) VALUES
('Digital Marketing Guide', 'Comprehensive guide to modern digital marketing strategies and techniques.', 29.99, 'digital-marketing-guide', 'active', 1),
('SEO Mastery Course', 'Learn advanced SEO techniques to rank higher in search engines.', 49.99, 'seo-mastery-course', 'active', 1),
('Social Media Toolkit', 'Complete toolkit for managing and growing your social media presence.', 39.99, 'social-media-toolkit', 'active', 1),
('Content Creation Bundle', 'Premium bundle of content creation tools and templates.', 59.99, 'content-creation-bundle', 'active', 1),
('Email Marketing Templates', 'Professional email marketing templates for various campaigns.', 19.99, 'email-marketing-templates', 'active', 0),
('Analytics Dashboard Pro', 'Advanced analytics dashboard for tracking marketing metrics.', 79.99, 'analytics-dashboard-pro', 'active', 0);
