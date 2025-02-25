<?php
require_once '../includes/db_connect.php';

try {
    // First ensure user exists
    $stmt = $pdo->prepare('INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->execute(['admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin']);
    echo "User added or already exists\n";

    // Now insert the content
    $stmt = $pdo->prepare('INSERT INTO content (user_id, title, content_type, content_text, excerpt, author, status, featured, slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

    $content = [
        [1, 'Getting Started with Dropshipping', 'blog', 'This is a comprehensive guide to starting your dropshipping business...', 'Learn how to start your dropshipping business from scratch with this comprehensive guide.', 'admin', 'published', 1, 'getting-started-dropshipping'],
        [1, 'SEO Best Practices 2025', 'blog', 'Learn the latest SEO techniques and strategies...', 'Stay ahead of the competition with these cutting-edge SEO strategies.', 'admin', 'published', 0, 'seo-best-practices-2025'],
        [1, 'Email Marketing Guide', 'tutorial', 'Step by step guide to email marketing...', 'Master email marketing with our detailed tutorial.', 'admin', 'published', 1, 'email-marketing-guide']
    ];

    foreach ($content as $item) {
        try {
            $stmt->execute($item);
            echo "Inserted: {$item[1]}\n";
        } catch (PDOException $e) {
            echo "Error inserting {$item[1]}: " . $e->getMessage() . "\n";
        }
    }

    echo "Done inserting content!\n";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
