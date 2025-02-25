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

// Get business guides
$stmt = $pdo->prepare("
    SELECT bg.*, u.username as author_name,
           (SELECT COUNT(*) FROM saved_items WHERE item_type = 'business_guide' AND item_id = bg.id) as save_count
    FROM business_guides bg 
    LEFT JOIN users u ON bg.author_id = u.id 
    WHERE bg.status = 'published'
    ORDER BY bg.created_at DESC
");
$stmt->execute();
$guides = $stmt->fetchAll();

// Get categories
$categories = [
    'startup' => 'Getting Started',
    'growth' => 'Advanced Strategies',
    'operations' => 'Game Operations',
    'marketing' => 'Community Building',
    'finance' => 'Tournament Management',
    'legal' => 'Rules & Guidelines'
];

// Set page title and additional styles
$pageTitle = 'Sidestacker Guides';
$additionalStyles = '
    .guide-card {
        transition: transform 0.3s ease-in-out;
    }
    .guide-card:hover {
        transform: translateY(-5px);
    }
';

include 'includes/head.php';
?>

    <?php include 'includes/header.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Sidestacker Guides</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Learn strategies, techniques, and best practices to improve your Sidestacker gameplay
            </p>
        </div>

        <!-- Categories -->
        <div class="flex flex-wrap gap-4 mb-8 justify-center">
            <?php foreach ($categories as $key => $name): ?>
            <a href="?category=<?php echo htmlspecialchars($key); ?>" 
               class="px-4 py-2 bg-white dark:bg-gray-800 rounded-full shadow-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <?php echo htmlspecialchars($name); ?>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($guides as $guide): ?>
            <div class="guide-card bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            <?php echo htmlspecialchars($guide['title']); ?>
                        </h3>
                        <?php if ($guide['is_premium']): ?>
                        <span class="px-3 py-1 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-full">
                            Premium
                        </span>
                        <?php endif; ?>
                    </div>

                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <?php echo htmlspecialchars($guide['description']); ?>
                    </p>

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-user mr-2"></i>
                            <?php echo htmlspecialchars($guide['author_name']); ?>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-bookmark mr-2"></i>
                            <?php echo $guide['save_count']; ?> saves
                        </div>
                    </div>

                    <?php if ($guide['is_premium'] && !isPremium($user)): ?>
                    <a href="premium.php" class="block w-full text-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:opacity-90 transition-opacity">
                        Upgrade to Access
                    </a>
                    <?php else: ?>
                    <div class="flex space-x-2">
                        <a href="guide.php?id=<?php echo $guide['id']; ?>" 
                           class="flex-1 text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Read Guide
                        </a>
                        <button onclick="saveGuide(<?php echo $guide['id']; ?>, this)" 
                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <i class="far fa-bookmark"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Save guide functionality
        async function saveGuide(guideId, button) {
            try {
                const response = await fetch('save_guide.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        guide_id: guideId
                    })
                });

                if (response.ok) {
                    const icon = button.querySelector('i');
                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                }
            } catch (error) {
                console.error('Error saving guide:', error);
            }
        }
    </script>
</body>
</html>
