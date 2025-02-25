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

// Get financial offers
$stmt = $pdo->prepare("
    SELECT fo.*, u.username as provider_name,
           (SELECT COUNT(*) FROM applications WHERE offer_id = fo.id) as application_count
    FROM financial_offers fo 
    LEFT JOIN users u ON fo.provider_id = u.id 
    WHERE fo.status = 'active' AND fo.deadline >= CURDATE()
    ORDER BY fo.created_at DESC
");
$stmt->execute();
$offers = $stmt->fetchAll();

// Get offer categories
$categories = [
    'tournament' => 'Tournament Prizes',
    'sponsorship' => 'Sponsorships',
    'prize_pool' => 'Prize Pools',
    'coaching' => 'Coaching Opportunities',
    'streaming' => 'Streaming Partnerships',
    'other' => 'Other Opportunities'
];

// Set page title and additional styles
$pageTitle = 'Financial Opportunities';
$additionalStyles = '
    .offer-card {
        transition: transform 0.3s ease-in-out;
    }
    .offer-card:hover {
        transform: translateY(-5px);
    }
';

include 'includes/head.php';
?>

    <?php include 'includes/header.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Financial Opportunities</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Discover tournaments, sponsorships, and other financial opportunities in the Sidestacker community
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
            <?php foreach ($offers as $offer): ?>
            <div class="offer-card bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            <?php echo htmlspecialchars($offer['title']); ?>
                        </h3>
                        <?php if ($offer['is_premium']): ?>
                        <span class="px-3 py-1 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-full">
                            Premium
                        </span>
                        <?php endif; ?>
                    </div>

                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <?php echo htmlspecialchars($offer['description']); ?>
                    </p>

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-user mr-2"></i>
                            <?php echo htmlspecialchars($offer['provider_name']); ?>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-dollar-sign mr-2"></i>
                            $<?php echo number_format($offer['amount'], 2); ?>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded p-4 mb-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Requirements:</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <?php echo htmlspecialchars($offer['requirements']); ?>
                        </p>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-calendar mr-2"></i>
                            Deadline: <?php echo date('M j, Y', strtotime($offer['deadline'])); ?>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-users mr-2"></i>
                            <?php echo $offer['application_count']; ?> applicants
                        </div>
                    </div>

                    <?php if ($offer['is_premium'] && !isPremium($user)): ?>
                    <a href="premium.php" class="block w-full text-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:opacity-90 transition-opacity">
                        Upgrade to Apply
                    </a>
                    <?php else: ?>
                    <a href="apply.php?offer_id=<?php echo $offer['id']; ?>" 
                       class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Apply Now
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (isPremium($user)): ?>
        <!-- Post New Offer (Premium Users Only) -->
        <div class="mt-12 text-center">
            <a href="post_offer.php" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i> Post New Opportunity
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
