<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db_connect.php';

try {
    // Get featured content
    $stmt = $pdo->prepare("SELECT c.* 
                          FROM content c 
                          WHERE c.status = 'published' AND c.featured = 1 
                          ORDER BY c.created_at DESC 
                          LIMIT 3");
    $stmt->execute();
    $featured_content = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get popular tools
    $stmt = $pdo->prepare("SELECT * FROM tools 
                          WHERE status = 'active' 
                          ORDER BY sort_order ASC 
                          LIMIT 6");
    $stmt->execute();
    $popular_tools = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get tool categories with counts
    $stmt = $pdo->prepare("SELECT category, COUNT(*) as count 
                          FROM tools 
                          WHERE status = 'active' 
                          GROUP BY category 
                          ORDER BY count DESC 
                          LIMIT 6");
    $stmt->execute();
    $tool_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get featured products
    $stmt = $pdo->prepare("SELECT * FROM products 
                          WHERE status = 'active' AND featured = 1 
                          ORDER BY created_at DESC 
                          LIMIT 4");
    $stmt->execute();
    $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize database if needed
    $tables_exist = true;
    try {
        $pdo->query("SELECT 1 FROM users LIMIT 1");
        $pdo->query("SELECT 1 FROM content LIMIT 1");
        $pdo->query("SELECT 1 FROM tools LIMIT 1");
        $pdo->query("SELECT 1 FROM products LIMIT 1");
    } catch (PDOException $e) {
        $tables_exist = false;
    }

    // Create tables if they don't exist
    if (!$tables_exist) {
        // Initialize database schema
        $sql = file_get_contents('database/init.sql');
        $pdo->exec($sql);
        
        // Add test data
        $test_data = file_get_contents('database/test_data.sql');
        $pdo->exec($test_data);
        
        $_SESSION['message'] = "Database initialized successfully with test data!";
    }

    // Get latest content
    $stmt = $pdo->prepare("SELECT c.* 
                          FROM content c 
                          WHERE c.status = 'published' 
                          ORDER BY c.created_at DESC 
                          LIMIT 6");
    $stmt->execute();
    $latest_content = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($latest_content)) {
        $_SESSION['warning'] = "No published content found. Please create some content first.";
    }

} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Debug output
if (isset($_GET['debug'])) {
    echo "<pre>";
    echo "Featured Content:\n";
    print_r($featured_content);
    echo "\nFeatured Products:\n";
    print_r($featured_products);
    echo "\nPopular Tools:\n";
    print_r($popular_tools);
    echo "\nLatest Content:\n";
    print_r($latest_content);
    echo "</pre>";
}

// Display any messages
if (isset($_SESSION['message'])) {
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">';
    echo '<span class="block sm:inline">' . htmlspecialchars($_SESSION['message']) . '</span>';
    echo '</div>';
    unset($_SESSION['message']);
}

if (isset($_SESSION['warning'])) {
    echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">';
    echo '<span class="block sm:inline">' . htmlspecialchars($_SESSION['warning']) . '</span>';
    echo '</div>';
    unset($_SESSION['warning']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">';
    echo '<span class="block sm:inline">' . htmlspecialchars($_SESSION['error']) . '</span>';
    echo '</div>';
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidestacker - Make Money Online & Digital Marketing</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0066FF',
                        secondary: '#F5F7FA',
                        accent: '#00C7FF',
                        dark: '#1A1A1A'
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'clash': ['"Clash Display"', 'sans-serif'],
                        'sf': ['"SF Pro Display"', 'system-ui']
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Top Navigation -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50 dark:bg-gray-900">
        <nav class="px-4 py-3 flex justify-between items-center max-w-6xl mx-auto">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-2.5 rounded-xl shadow-lg transform hover:scale-105 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-blue-800">Sidestacker</span>
                    <span class="text-xs text-gray-500 font-medium tracking-wider -mt-1">BUSINESS TOOLS</span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Theme Toggle -->
                <button id="theme-toggle" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                <!-- Menu Toggle -->
                <button onclick="toggleSidenav()" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </nav>
    </header>

    <!-- Sidenav Menu -->
    <div id="sidenav-menu" class="fixed inset-0 bg-gray-900/95 z-50 transform translate-x-full transition-transform duration-300">
        <div class="h-full overflow-y-auto">
            <!-- Menu Header -->
            <div class="flex justify-between items-center p-3 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Menu</h2>
                <button onclick="toggleSidenav()" class="text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Menu Items -->
            <div class="p-2 md:p-3 lg:max-w-3xl lg:mx-auto">
                <div class="grid grid-cols-3 gap-1.5 md:gap-2">
                    <!-- Home -->
                    <a href="index.php" class="aspect-square bg-gray-800 rounded-lg p-2 md:p-3 flex flex-col items-center justify-center hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="text-white text-[10px] md:text-xs text-center">Home</span>
                    </a>

                    <!-- Courses -->
                    <a href="courses.php" class="aspect-square bg-gray-800 rounded-lg p-2 md:p-3 flex flex-col items-center justify-center hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span class="text-white text-[10px] md:text-xs text-center">Courses</span>
                    </a>

                    <!-- Financial Offers -->
                    <a href="financial_offers.php" class="aspect-square bg-gray-800 rounded-lg p-2 md:p-3 flex flex-col items-center justify-center hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-white text-[10px] md:text-xs text-center">Financial Offers</span>
                    </a>

                    <!-- Business Guides -->
                    <a href="business_guides.php" class="aspect-square bg-gray-800 rounded-lg p-2 md:p-3 flex flex-col items-center justify-center hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-white text-[10px] md:text-xs text-center">Business Guides</span>
                    </a>

                    <!-- Tools & Software -->
                    <a href="tools_software.php" class="aspect-square bg-gray-800 rounded-lg p-2 md:p-3 flex flex-col items-center justify-center hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-white text-[10px] md:text-xs text-center">Tools & Software</span>
                    </a>

                    <!-- Queries & Polls -->
                    <a href="queries.php" class="aspect-square bg-gray-800 rounded-lg p-2 md:p-3 flex flex-col items-center justify-center hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-white text-[10px] md:text-xs text-center">Queries & Polls</span>
                    </a>

                    <!-- Shop -->
                    <a href="shop.php" class="aspect-square bg-gray-800 rounded-lg p-2 md:p-3 flex flex-col items-center justify-center hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="text-white text-[10px] md:text-xs text-center">Shop</span>
                    </a>

                    <!-- Premium -->
                    <a href="premium.php" class="aspect-square bg-gray-800 rounded-lg p-2 md:p-3 flex flex-col items-center justify-center hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        <span class="text-white text-[10px] md:text-xs text-center">Premium</span>
                    </a>

                    <?php if (!$isLoggedIn): ?>
                    <!-- Login -->
                    <a href="login.php" class="aspect-square bg-gray-800 rounded-lg p-2 md:p-3 flex flex-col items-center justify-center hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="text-white text-[10px] md:text-xs text-center">Login</span>
                    </a>

                    <!-- Register -->
                    <a href="register.php" class="aspect-square bg-gray-800 rounded-lg p-2 md:p-3 flex flex-col items-center justify-center hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span class="text-white text-[10px] md:text-xs text-center">Register</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleSidenav() {
        const sidenav = document.getElementById('sidenav-menu');
        sidenav.classList.toggle('translate-x-full');
        sidenav.classList.toggle('translate-x-0');
    }
    </script>

    <!-- Main Content Area -->
    <div class="mt-16 mb-20">
        <div class="max-w-7xl mx-auto px-4 flex flex-col lg:flex-row justify-between">
            <!-- Main Content -->
            <main class="flex-1 max-w-3xl">
                <!-- Global Search -->
                <div class="py-6">
                    <div class="relative max-w-2xl">
                        <input type="text" placeholder="Search content, tools, tutorials..." 
                               class="w-full px-4 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-primary focus:border-transparent"
                               aria-label="Search">
                        <button class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-2v-4m2 4h-4m2 2h4m-2 2v4m-4-4h-4m-2 2h4m-2-2v-4m2-4h4m-2 2h-4"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Latest Content -->
                <div class="py-6">
                    <h2 class="text-2xl font-clash font-bold mb-4 dark:text-white">Latest Content</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php if (!empty($latest_content)): ?>
                            <?php foreach ($latest_content as $content): ?>
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden group hover:shadow-md transition-shadow">
                                    <div class="relative aspect-[16/9] overflow-hidden">
                                        <img src="https://picsum.photos/seed/<?= $content['id'] ?>/800/450" 
                                             alt="<?= htmlspecialchars($content['title']) ?>"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <div class="absolute bottom-0 left-0 right-0 p-4">
                                            <h3 class="text-lg font-semibold text-white">
                                                <?= htmlspecialchars($content['title']) ?>
                                            </h3>
                                            <p class="text-sm text-gray-200 mt-1">
                                                By <?= htmlspecialchars($content['author']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-600 dark:text-gray-400">No content available yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Featured Content -->
                <div class="py-6">
                    <h2 class="text-2xl font-clash font-bold mb-4 dark:text-white">Featured Content</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php if (!empty($featured_content)): ?>
                            <?php foreach ($featured_content as $content): ?>
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden group hover:shadow-md transition-shadow">
                                    <div class="relative aspect-[16/9] overflow-hidden">
                                        <img src="https://picsum.photos/seed/<?= $content['id'] ?>/800/450" 
                                             alt="<?= htmlspecialchars($content['title']) ?>"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <div class="absolute bottom-0 left-0 right-0 p-4">
                                            <h3 class="text-lg font-semibold text-white">
                                                <?= htmlspecialchars($content['title']) ?>
                                            </h3>
                                            <p class="text-sm text-gray-200 mt-1">
                                                By <?= htmlspecialchars($content['author']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-600 dark:text-gray-400">No featured content available.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Featured Products Section -->
                <div class="py-6">
                    <h2 class="text-2xl font-clash font-bold mb-6 dark:text-white">Featured Products</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php if (!empty($featured_products)): ?>
                            <?php foreach ($featured_products as $product): ?>
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden flex flex-col h-full">
                                    <!-- Product Image -->
                                    <div class="aspect-[4/3] w-full overflow-hidden">
                                        <img src="<?php echo !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'assets/images/products/' . htmlspecialchars($product['slug']) . '.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <!-- Product Details -->
                                    <div class="p-5 flex flex-col flex-1">
                                        <h3 class="text-lg font-semibold mb-2 dark:text-white hover:text-primary transition-colors">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                            <?php echo substr(htmlspecialchars($product['description']), 0, 120) . '...'; ?>
                                        </p>
                                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                                            <span class="text-xl font-bold text-primary dark:text-blue-400">
                                                $<?php echo number_format($product['price'], 2); ?>
                                            </span>
                                            <a href="product.php?id=<?php echo htmlspecialchars($product['id']); ?>" 
                                               class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="text-center mt-8">
                        <a href="products.php" class="inline-flex items-center justify-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                            View All Products
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Popular Tools Section -->
                <div class="py-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-clash font-bold dark:text-white">Popular Tools</h2>
                        <a href="tools.php" class="text-primary hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 flex items-center">
                            View All Tools
                            <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($popular_tools as $tool): ?>
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="text-2xl text-blue-500 dark:text-blue-400">
                                        <i class="fas <?php echo htmlspecialchars($tool['icon_class']); ?>"></i>
                                    </div>
                                    <span class="px-3 py-1 text-sm rounded-full <?php echo $tool['type'] === 'premium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($tool['type'])); ?>
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold mb-2 dark:text-white"><?php echo htmlspecialchars($tool['name']); ?></h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4"><?php echo htmlspecialchars($tool['description']); ?></p>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($tool['category']); ?></span>
                                    <a href="/tool/<?php echo htmlspecialchars($tool['slug']); ?>" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300">
                                        Use Tool <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>

            <!-- Sidebar with Ad Slots - Only visible on desktop -->
            <aside class="hidden lg:block w-[400px] space-y-8 sticky top-20 self-start ml-auto pl-20 pr-4">
                <!-- Sidebar Ad Slot 1 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm mx-auto w-[360px]">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Advertisement</p>
                    <div class="w-full h-72 bg-gray-100 dark:bg-gray-700 rounded-lg"></div>
                </div>

                <!-- Sidebar Ad Slot 2 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm mx-auto w-[360px]">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Advertisement</p>
                    <div class="w-full h-72 bg-gray-100 dark:bg-gray-700 rounded-lg"></div>
                </div>

                <!-- Sidebar Ad Slot 3 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm mx-auto w-[360px]">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Advertisement</p>
                    <div class="w-full h-72 bg-gray-100 dark:bg-gray-700 rounded-lg"></div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Newsletter Popup -->
    <div id="newsletter-popup" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-t-2xl absolute bottom-0 left-0 right-0 p-6 transform transition-transform duration-300">
            <button class="absolute top-4 right-4" onclick="closeNewsletter()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <h3 class="text-2xl font-clash font-bold mb-2 dark:text-white">Join Our Newsletter</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Get the latest content and tools delivered to your inbox!</p>
            <form class="space-y-4">
                <input type="email" placeholder="Enter your email" class="w-full p-3 rounded-xl border dark:bg-gray-800 dark:border-gray-700">
                <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl hover:bg-opacity-90">Subscribe</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Search Modal -->
    <div id="search-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-t-2xl absolute bottom-0 left-0 right-0 p-6 transform transition-transform duration-300">
            <div class="flex items-center mb-4">
                <input type="text" placeholder="Search Sidestacker..." 
                       class="w-full p-3 rounded-xl border focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="space-y-2">
                <p class="text-sm text-gray-500">Recent Searches</p>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-secondary rounded-full text-sm">Dropshipping</span>
                    <span class="px-3 py-1 bg-secondary rounded-full text-sm">SEO Tools</span>
                    <span class="px-3 py-1 bg-secondary rounded-full text-sm">Marketing</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide search modal
        document.getElementById('search-btn').addEventListener('click', function() {
            document.getElementById('search-modal').classList.toggle('hidden');
        });

        // Show/hide sidenav menu
        document.getElementById('more-btn').addEventListener('click', function() {
            document.getElementById('sidenav-menu').classList.toggle('translate-x-full');
        });

        document.getElementById('close-sidenav').addEventListener('click', function() {
            document.getElementById('sidenav-menu').classList.add('translate-x-full');
        });

        // Show newsletter popup after 5 seconds
        setTimeout(function() {
            document.getElementById('newsletter-popup').classList.remove('hidden');
        }, 5000);

        // Close newsletter popup
        function closeNewsletter() {
            document.getElementById('newsletter-popup').classList.add('hidden');
        }
    </script>
</body>
</html>
