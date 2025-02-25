<?php
require_once 'includes/db_connect.php';
require_once 'includes/session_manager.php';

$sessionManager = SessionManager::getInstance();

// Check if user is logged in
if (!$sessionManager->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get user info including premium status
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$sessionManager->getUserId()]);
$user = $stmt->fetch();

// Function to check if user is premium
function isPremium($user) {
    return isset($user['is_premium']) && $user['is_premium'] && 
           (!isset($user['premium_until']) || strtotime($user['premium_until']) > time());
}

// Get tools and software
$stmt = $pdo->prepare("
    SELECT t.*, 
           (SELECT COUNT(*) FROM tool_ratings WHERE tool_id = t.id) as rating_count,
           (SELECT AVG(rating) FROM tool_ratings WHERE tool_id = t.id) as avg_rating
    FROM tools t 
    WHERE t.status = 'active'
    ORDER BY t.created_at DESC
");
$stmt->execute();
$tools = $stmt->fetchAll();

// Tool categories
$categories = [
    'productivity' => 'Productivity Tools',
    'marketing' => 'Marketing Tools',
    'finance' => 'Financial Tools',
    'design' => 'Design Tools',
    'communication' => 'Communication Tools',
    'analytics' => 'Analytics Tools'
];

// Set page title and any additional styles
$pageTitle = 'Tools & Software';
$additionalStyles = '
    .tool-card {
        transition: transform 0.3s ease-in-out;
    }
    .tool-card:hover {
        transform: translateY(-5px);
    }
';

include 'includes/head.php';
?>

    <?php include 'includes/header.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Tools & Software</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Discover powerful tools and software to enhance your Sidestacker gameplay and strategy.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($tools as $tool): ?>
                <div class="tool-card bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                <?php echo htmlspecialchars($tool['name']); ?>
                            </h3>
                            <?php if ($tool['type'] === 'premium'): ?>
                                <span class="px-3 py-1 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-full">
                                    Premium
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            <?php echo htmlspecialchars($tool['description']); ?>
                        </p>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <i class="<?php echo htmlspecialchars($tool['icon_class']); ?> text-gray-400 mr-2"></i>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo isset($categories[$tool['category']]) ? $categories[$tool['category']] : ucfirst($tool['category']); ?>
                                </span>
                            </div>
                            <?php if ($tool['rating_count'] > 0): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        <?php echo number_format($tool['avg_rating'], 1); ?>
                                        (<?php echo $tool['rating_count']; ?> reviews)
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($tool['type'] === 'premium' && !isPremium($user)): ?>
                            <a href="premium.php" class="block w-full text-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:opacity-90 transition-opacity">
                                Upgrade to Access
                            </a>
                        <?php else: ?>
                            <a href="tools/<?php echo htmlspecialchars($tool['slug']); ?>" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Use Tool
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
