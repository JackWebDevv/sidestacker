<?php
require_once 'includes/db_connect.php';
require_once 'includes/auth_middleware.php';
require_once 'includes/helpers.php';

// Require login and get session manager
$sessionManager = requireLogin();
$userId = $sessionManager->getUserId();

// Get user info including premium status
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Function to check if user is premium
function isPremium($user) {
    return isset($user['is_premium']) && $user['is_premium'] && 
           (!isset($user['premium_until']) || strtotime($user['premium_until']) > time());
}

// Get user's content
$stmt = $pdo->prepare("SELECT * FROM content WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$contents = $stmt->fetchAll();

// Get user's saved items (including products)
$saved_stmt = $pdo->prepare("
    SELECT si.*, 
           p.name as product_name, p.slug as product_slug, p.price as product_price, 
           p.description as product_description
    FROM saved_items si
    LEFT JOIN products p ON si.item_type = 'product' AND si.item_id = p.id
    WHERE si.user_id = ? AND si.item_type = 'product'
    ORDER BY si.created_at DESC
");
$saved_stmt->execute([$userId]);
$saved_products = $saved_stmt->fetchAll();

// Get user's saved items
$saved_stmt = $pdo->prepare("
    SELECT si.*, c.title as content_title, c.slug as content_slug, c.content_type,
           t.name as tool_name, t.slug as tool_slug,
           p.name as product_name, p.slug as product_slug, p.price as product_price
    FROM saved_items si
    LEFT JOIN content c ON si.item_type = 'content' AND si.item_id = c.id
    LEFT JOIN tools t ON si.item_type = 'tool' AND si.item_id = t.id
    LEFT JOIN products p ON si.item_type = 'product' AND si.item_id = p.id
    WHERE si.user_id = ?
    ORDER BY si.created_at DESC
    LIMIT 5
");
$saved_stmt->execute([$userId]);
$saved_items = $saved_stmt->fetchAll();

// Get user's history
$history_stmt = $pdo->prepare("
    SELECT uh.*, c.title as content_title, c.slug as content_slug, c.content_type,
           t.name as tool_name, t.slug as tool_slug
    FROM user_history uh
    LEFT JOIN content c ON uh.item_type = 'content' AND uh.item_id = c.id
    LEFT JOIN tools t ON uh.item_type = 'tool' AND uh.item_id = t.id
    WHERE uh.user_id = ?
    ORDER BY uh.created_at DESC
    LIMIT 10
");
$history_stmt->execute([$userId]);
$history_items = $history_stmt->fetchAll();

// Get flash message if any
$flashMessage = $sessionManager->getFlashMessage();

// Get user's recommended items
$stmt = $pdo->prepare("
    SELECT DISTINCT c.*, 
           CASE 
               WHEN uh.item_type = 'content' THEN 'content'
               WHEN uh.item_type = 'tool' THEN 'tool'
               ELSE 'product'
           END as type
    FROM user_history uh
    LEFT JOIN content c ON uh.item_type = 'content' AND uh.item_id = c.id
    WHERE uh.user_id = ?
    ORDER BY uh.created_at DESC
    LIMIT 5
");
$stmt->execute([$userId]);
$recommended_items = $stmt->fetchAll();

// Get saved business plans
$stmt = $pdo->prepare("
    SELECT bp.* FROM business_plans bp
    INNER JOIN saved_items si ON bp.id = si.item_id AND si.item_type = 'business_plan'
    WHERE si.user_id = ?
    ORDER BY si.created_at DESC
");
$stmt->execute([$userId]);
$saved_business_plans = $stmt->fetchAll();

// Get saved jobs
$stmt = $pdo->prepare("
    SELECT j.* FROM jobs j
    INNER JOIN saved_items si ON j.id = si.item_id AND si.item_type = 'job'
    WHERE si.user_id = ?
    ORDER BY si.created_at DESC
");
$stmt->execute([$userId]);
$saved_jobs = $stmt->fetchAll();

// Check if user is premium
$is_premium = isPremium($user);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sidestacker</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <?php include 'includes/header.php'; ?>

    <div class="max-w-7xl mx-auto px-4 mt-20 mb-20">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar -->
            <div class="w-full lg:w-64">
                <!-- User Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <div class="text-center">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            <?= htmlspecialchars($user['username']) ?>
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Member since <?= date('M Y', strtotime($user['created_at'])) ?>
                        </p>
                        <?php if ($is_premium): ?>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Premium Member
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Premium Features -->
                <?php if ($is_premium): ?>
                    <!-- Ad Database -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-ad mr-2"></i>Ad Database
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Manage your advertisements and reach your target audience.
                        </p>
                        <a href="ad_database.php" class="inline-block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Manage Ads
                        </a>
                    </div>

                    <!-- Freelance Gigs -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-briefcase mr-2"></i>Freelance Gigs
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Post and manage your freelance opportunities.
                        </p>
                        <a href="freelance_gigs.php" class="inline-block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Manage Gigs
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Premium Upgrade Prompt -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-sm p-6 mb-6 text-white">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-crown mr-2"></i>Upgrade to Premium
                        </h3>
                        <p class="text-sm mb-4">
                            Unlock premium features:
                            <ul class="list-disc list-inside mt-2 space-y-1 text-sm">
                                <li>Submit advertisements</li>
                                <li>Post freelance gigs</li>
                                <li>Access premium tools</li>
                                <li>And much more!</li>
                            </ul>
                        </p>
                        <a href="premium.php" class="inline-block w-full text-center px-4 py-2 bg-white text-blue-600 rounded-md hover:bg-gray-100">
                            Upgrade Now
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Navigation -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <nav class="flex flex-col">
                        <a href="dashboard.php" class="flex items-center px-4 py-3 bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-600 text-blue-700 dark:text-blue-200">
                            <i class="fas fa-tachometer-alt w-5 h-5"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                        <a href="admin/create_content.php" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-plus w-5 h-5"></i>
                            <span class="ml-3">Create Content</span>
                        </a>
                        <a href="profile.php" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-user w-5 h-5"></i>
                            <span class="ml-3">Profile</span>
                        </a>
                        <button onclick="openAffiliateModal()" class="w-full text-left flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-money-bill w-5 h-5"></i>
                            <span class="ml-3">Affiliate Program</span>
                        </button>
                        <div class="relative">
                            <a href="#" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-users w-5 h-5"></i>
                                <span class="ml-3">Communities</span>
                                <span class="absolute right-4 px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Coming Soon</span>
                            </a>
                        </div>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 space-y-8">
                <!-- Communities Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold font-heading text-gray-900 dark:text-white">Communities</h2>
                        <span class="px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-800 rounded-full">Coming Soon</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Join communities of like-minded developers to share knowledge, collaborate on projects, and grow together.</p>
                </div>

                <!-- Saved Items Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold font-heading text-gray-900 dark:text-white">Saved Items</h2>
                    </div>
                    <?php if (empty($saved_items)): ?>
                        <p class="text-gray-500 dark:text-gray-400">No saved items yet.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($saved_items as $item): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-1">
                                        <?php if ($item['item_type'] === 'content'): ?>
                                            <a href="content.php?slug=<?php echo urlencode($item['content_slug']); ?>" 
                                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                                <?php echo htmlspecialchars($item['content_title']); ?>
                                            </a>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                <?php echo ucfirst($item['content_type']); ?>
                                            </p>
                                        <?php elseif ($item['item_type'] === 'tool'): ?>
                                            <a href="tools.php?slug=<?php echo urlencode($item['tool_slug']); ?>" 
                                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                                <?php echo htmlspecialchars($item['tool_name']); ?>
                                            </a>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Tool</p>
                                        <?php elseif ($item['item_type'] === 'product'): ?>
                                            <a href="product.php?slug=<?php echo urlencode($item['product_slug']); ?>" 
                                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                                <?php echo htmlspecialchars($item['product_name']); ?>
                                            </a>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Product - $<?php echo number_format($item['product_price'], 2); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <button onclick="unsaveItem(<?php echo $item['id']; ?>, this)" 
                                            class="ml-4 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Saved Products Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold font-heading text-gray-900 dark:text-white">Saved Products</h2>
                    </div>
                    <?php if (empty($saved_products)): ?>
                        <p class="text-gray-500 dark:text-gray-400">No saved products yet.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($saved_products as $product): ?>
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            <a href="product.php?slug=<?php echo urlencode($product['product_slug']); ?>" 
                                               class="hover:text-blue-600 dark:hover:text-blue-400">
                                                <?php echo htmlspecialchars($product['product_name']); ?>
                                            </a>
                                        </h3>
                                        <?php if (!empty($product['product_description'])): ?>
                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                                <?php echo htmlspecialchars(substr($product['product_description'], 0, 100)) . '...'; ?>
                                            </p>
                                        <?php endif; ?>
                                        <div class="mt-4 flex items-center justify-between">
                                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                                $<?php echo number_format($product['product_price'], 2); ?>
                                            </span>
                                            <button onclick="unsaveItem(<?php echo $product['id']; ?>, this)"
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Saved Tools Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold font-heading text-gray-900 dark:text-white">Saved Tools</h2>
                    </div>
                    <?php if (empty($saved_items)): ?>
                        <p class="text-gray-500 dark:text-gray-400">No saved tools yet.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($saved_items as $item): ?>
                                <?php if ($item['item_type'] === 'tool'): ?>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($item['tool_name']); ?>
                                            </h3>
                                            <button 
                                                class="save-tool-btn text-yellow-400 hover:text-yellow-500" 
                                                data-tool-id="<?php echo $item['item_id']; ?>"
                                                title="Unsave tool"
                                            >
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </div>
                                        <a href="tools.php?slug=<?php echo urlencode($item['tool_slug']); ?>" 
                                           class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            <span>Open Tool</span>
                                            <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- History Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold font-heading text-gray-900 dark:text-white mb-6">Recent Activity</h2>
                    <?php if (empty($history_items)): ?>
                        <p class="text-gray-600 dark:text-gray-300">No recent activity yet.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($history_items as $item): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas <?php echo $item['item_type'] === 'content' ? 'fa-file-alt' : 'fa-tools'; ?> text-blue-600 mr-3"></i>
                                        <div>
                                            <a href="<?php echo $item['item_type'] === 'content' ? 'content.php?slug=' . urlencode($item['content_slug']) : 'tools.php?slug=' . urlencode($item['tool_slug']); ?>" 
                                               class="font-medium text-gray-900 dark:text-white hover:text-blue-600">
                                                <?php echo htmlspecialchars($item['item_type'] === 'content' ? $item['content_title'] : $item['tool_name']); ?>
                                            </a>
                                            <div class="flex items-center text-sm text-gray-500 mt-1">
                                                <span class="capitalize"><?php echo $item['action_type']; ?>ed</span>
                                                <span class="mx-2">&bull;</span>
                                                <span><?php echo human_time_diff(strtotime($item['created_at'])); ?> ago</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recommended Items -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold font-heading text-gray-900 dark:text-white">Recommended Items</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($recommended_items as $item): ?>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h3 class="font-semibold mb-2"><?= htmlspecialchars($item['title']) ?></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300"><?= htmlspecialchars($item['description']) ?></p>
                                <span class="inline-block mt-2 text-xs font-medium text-blue-600 dark:text-blue-400"><?= ucfirst($item['type']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Saved Business Plans -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold font-heading text-gray-900 dark:text-white">Saved Business Plans</h2>
                    </div>
                    <?php if (empty($saved_business_plans)): ?>
                        <p class="text-gray-500 dark:text-gray-400">No saved business plans yet.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($saved_business_plans as $plan): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <h3 class="font-semibold"><?= htmlspecialchars($plan['title']) ?></h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300"><?= htmlspecialchars($plan['industry']) ?></p>
                                    </div>
                                    <a href="business-plan.php?id=<?= $plan['id'] ?>" class="text-blue-600 hover:text-blue-700 dark:text-blue-400">View Plan</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Saved Jobs -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold font-heading text-gray-900 dark:text-white">Saved Jobs</h2>
                    </div>
                    <?php if (empty($saved_jobs)): ?>
                        <p class="text-gray-500 dark:text-gray-400">No saved jobs yet.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($saved_jobs as $job): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <h3 class="font-semibold"><?= htmlspecialchars($job['title']) ?></h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300"><?= htmlspecialchars($job['company']) ?></p>
                                    </div>
                                    <a href="job.php?id=<?= $job['id'] ?>" class="text-blue-600 hover:text-blue-700 dark:text-blue-400">View Job</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!$is_premium): ?>
                <!-- Upgrade Account Section -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-sm p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold font-heading mb-2">Upgrade to Premium</h2>
                            <p class="text-blue-100">Get access to premium features including job posting and advanced tools.</p>
                        </div>
                        <a href="premium.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                            Upgrade Now
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Your Content Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold font-heading text-gray-900 dark:text-white">Your Content</h1>
                        <a href="admin/create_content.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Create New
                        </a>
                    </div>

                    <?php if (empty($contents)): ?>
                        <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700 dark:text-blue-200">
                                        You haven't created any content yet.
                                        <a href="admin/create_content.php" class="font-medium underline">Create your first post!</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="space-y-6">
                            <?php foreach ($contents as $content): ?>
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                    <div class="p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $content['content_type'] === 'blog' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'; ?>">
                                                <i class="fas fa-<?php echo $content['content_type'] === 'blog' ? 'blog' : 'chalkboard-teacher'; ?> mr-2"></i>
                                                <?php echo ucfirst($content['content_type']); ?>
                                            </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                <?php echo date('M j, Y', strtotime($content['created_at'])); ?>
                                            </span>
                                        </div>
                                        <h2 class="text-xl font-bold font-heading text-gray-900 dark:text-white mb-3">
                                            <?php echo htmlspecialchars($content['title']); ?>
                                        </h2>
                                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                                            <?php echo htmlspecialchars($content['excerpt']); ?>
                                        </p>
                                        <div class="flex justify-between items-center">
                                            <div class="flex space-x-2">
                                                <a href="admin/edit_content.php?id=<?php echo $content['id']; ?>" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <i class="fas fa-edit mr-2"></i>
                                                    Edit
                                                </a>
                                                <button onclick="deleteContent(<?php echo $content['id']; ?>)" class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 dark:text-red-200 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900">
                                                    <i class="fas fa-trash-alt mr-2"></i>
                                                    Delete
                                                </button>
                                            </div>
                                            <a href="view_content.php?slug=<?php echo $content['slug']; ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                                View <i class="fas fa-arrow-right ml-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Affiliate Modal -->
    <div id="affiliateModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold font-heading text-gray-900 dark:text-white">Affiliate Program</h2>
                    <button onclick="closeAffiliateModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Affiliate Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-300 mb-1">Total Earnings</h3>
                        <p class="text-2xl font-bold text-blue-800 dark:text-blue-200">$0.00</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-700 dark:text-green-300 mb-1">Referrals</h3>
                        <p class="text-2xl font-bold text-green-800 dark:text-green-200">0</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-700 dark:text-purple-300 mb-1">Commission Rate</h3>
                        <p class="text-2xl font-bold text-purple-800 dark:text-purple-200">30%</p>
                    </div>
                </div>

                <!-- Affiliate Link -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Your Affiliate Link</h3>
                    <div class="flex gap-2">
                        <input type="text" 
                               value="https://sidestacker.com/?ref=<?php echo urlencode($user['username']); ?>" 
                               class="flex-1 px-4 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" 
                               readonly
                               id="affiliateLink">
                        <button onclick="copyAffiliateLink()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-copy mr-2"></i>Copy
                        </button>
                    </div>
                </div>

                <!-- How It Works -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">How It Works</h3>
                    <div class="space-y-4 text-gray-600 dark:text-gray-300">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-300 mr-3">1</div>
                            <p>Share your unique affiliate link with your audience</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-300 mr-3">2</div>
                            <p>When someone signs up using your link, they're tracked as your referral</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-300 mr-3">3</div>
                            <p>Earn 30% commission on all purchases made by your referrals</p>
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <p>By participating in the affiliate program, you agree to our <a href="#" class="text-blue-600 hover:underline">Affiliate Terms & Conditions</a>.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAffiliateModal() {
            document.getElementById('affiliateModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAffiliateModal() {
            document.getElementById('affiliateModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function copyAffiliateLink() {
            const linkInput = document.getElementById('affiliateLink');
            linkInput.select();
            document.execCommand('copy');
            
            // Show feedback
            const button = event.currentTarget;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
            button.classList.add('bg-green-600');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600');
            }, 2000);
        }

        // Close modal when clicking outside
        document.getElementById('affiliateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAffiliateModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('affiliateModal').classList.contains('hidden')) {
                closeAffiliateModal();
            }
        });
    </script>

    <script>
        function deleteContent(id) {
            if (confirm('Are you sure you want to delete this content?')) {
                fetch(`admin/delete_content.php?id=${id}`, {
                    method: 'DELETE',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting content');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting content');
                });
            }
        }
    </script>

    <script>
        async function unsaveItem(itemId, button) {
            if (!confirm('Are you sure you want to remove this item from your saved items?')) {
                return;
            }

            try {
                const response = await fetch('save_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${itemId}&action=unsave`
                });

                const data = await response.json();
                if (data.success) {
                    // Remove the product card
                    const productCard = button.closest('.bg-white.rounded-lg.shadow');
                    productCard.remove();

                    // Check if there are no more products
                    const productsGrid = document.querySelector('.grid');
                    if (productsGrid && !productsGrid.children.length) {
                        productsGrid.parentElement.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No saved products yet.</p>';
                    }
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while removing the item');
            }
        }
    </script>

    <script>
        // Function to save/unsave a tool
        async function toggleSaveTool(toolId, button) {
            try {
                const response = await fetch('save_tool.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `tool_id=${toolId}`
                });
                
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Update button appearance
                if (data.status === 'saved') {
                    button.classList.remove('text-gray-400');
                    button.classList.add('text-yellow-400');
                    button.title = 'Unsave tool';
                } else {
                    button.classList.remove('text-yellow-400');
                    button.classList.add('text-gray-400');
                    button.title = 'Save tool';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to save tool. Please try again.');
            }
        }

        // Add click handlers to save buttons
        document.addEventListener('DOMContentLoaded', function() {
            const saveButtons = document.querySelectorAll('.save-tool-btn');
            saveButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const toolId = this.getAttribute('data-tool-id');
                    toggleSaveTool(toolId, this);
                });
            });
        });
    </script>
</body>
</html>
