<?php
session_start();
require_once 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user info including premium status
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Function to check if user is premium
function isPremium($user) {
    return isset($user['is_premium']) && $user['is_premium'] && 
           (!isset($user['premium_until']) || strtotime($user['premium_until']) > time());
}

// Get queries and polls
$stmt = $pdo->prepare("
    SELECT q.*, u.username as author_name,
           (SELECT COUNT(*) FROM responses WHERE query_id = q.id) as response_count,
           (SELECT COUNT(*) FROM query_votes WHERE query_id = q.id) as vote_count
    FROM queries q
    LEFT JOIN users u ON q.user_id = u.id
    WHERE q.status = 'active'
    ORDER BY q.created_at DESC
");
$stmt->execute();
$queries = $stmt->fetchAll();

// Categories for queries
$categories = [
    'market_research' => 'Market Research',
    'customer_feedback' => 'Customer Feedback',
    'product_development' => 'Product Development',
    'business_strategy' => 'Business Strategy',
    'industry_trends' => 'Industry Trends'
];
?>

<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queries & Polls - Sidestacker</title>
    <link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <?php include 'includes/header.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Queries & Polls</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Share insights and gather feedback from the community.
            </p>
        </div>

        <!-- Categories -->
        <div class="flex flex-wrap gap-4 mb-8 justify-center">
            <?php foreach ($categories as $key => $name): ?>
            <a href="?category=<?= $key ?>" class="px-4 py-2 bg-white dark:bg-gray-800 rounded-full shadow-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <?= htmlspecialchars($name) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if (isPremium($user)): ?>
        <!-- Create New Query -->
        <div class="mb-8">
            <form action="create_query.php" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="mb-4">
                    <label for="query" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Ask a Question or Create a Poll
                    </label>
                    <textarea id="query" name="query" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Post Query
                    </button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Create Your Own Queries</h2>
                    <p class="text-white/90">Upgrade to premium to post queries and create polls.</p>
                </div>
                <a href="premium.php" class="px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Upgrade Now
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Queries List -->
        <div class="space-y-6">
            <?php foreach ($queries as $query): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="mr-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($query['title']) ?>
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Posted by <?= htmlspecialchars($query['author_name']) ?> • 
                                    <?= date('M j, Y', strtotime($query['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                        <?php if ($query['is_premium']): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Premium
                        </span>
                        <?php endif; ?>
                    </div>

                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <?= htmlspecialchars($query['content']) ?>
                    </p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-comment mr-2"></i>
                                <?= $query['response_count'] ?> responses
                            </span>
                            <span class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-chart-bar mr-2"></i>
                                <?= $query['vote_count'] ?> votes
                            </span>
                        </div>

                        <?php if ($query['is_premium'] && !isPremium($user)): ?>
                        <a href="premium.php" class="inline-flex items-center text-blue-600 hover:text-blue-500">
                            Unlock <i class="fas fa-lock ml-2"></i>
                        </a>
                        <?php else: ?>
                        <a href="query.php?id=<?= $query['id'] ?>" class="inline-flex items-center text-blue-600 hover:text-blue-500">
                            View Details <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
