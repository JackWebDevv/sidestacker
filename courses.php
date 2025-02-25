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

// Get courses
$stmt = $pdo->prepare("
    SELECT c.*, u.username as author_name 
    FROM courses c 
    LEFT JOIN users u ON c.author_id = u.id 
    ORDER BY c.created_at DESC
");
$stmt->execute();
$courses = $stmt->fetchAll();

// Set page title and any additional styles
$pageTitle = 'Courses';
$additionalStyles = '
    .course-card {
        transition: transform 0.3s ease-in-out;
    }
    .course-card:hover {
        transform: translateY(-5px);
    }
';

// Include header
include 'includes/head.php';
?>

    <?php include 'includes/header.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Educational Courses</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Enhance your knowledge with our comprehensive courses on Sidestacker strategy and gameplay.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($courses as $course): ?>
                <div class="course-card bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <?php if ($course['thumbnail_url']): ?>
                        <img src="<?php echo htmlspecialchars($course['thumbnail_url']); ?>" 
                             alt="<?php echo htmlspecialchars($course['title']); ?>" 
                             class="w-full h-48 object-cover">
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                <?php echo htmlspecialchars($course['title']); ?>
                            </h3>
                            <?php if ($course['is_premium']): ?>
                                <span class="px-3 py-1 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-full">
                                    Premium
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            <?php echo htmlspecialchars($course['description']); ?>
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                By <?php echo htmlspecialchars($course['author_name']); ?>
                            </div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                <?php if ($course['price'] > 0): ?>
                                    $<?php echo number_format($course['price'], 2); ?>
                                <?php else: ?>
                                    <span class="text-green-600 dark:text-green-400">Free</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($course['is_premium'] && !isPremium($user)): ?>
                            <a href="premium.php" class="mt-4 block w-full text-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:opacity-90 transition-opacity">
                                Upgrade to Access
                            </a>
                        <?php else: ?>
                            <a href="course.php?id=<?php echo $course['id']; ?>" class="mt-4 block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Start Learning
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
