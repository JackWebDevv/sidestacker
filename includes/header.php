<?php
require_once __DIR__ . '/auth_middleware.php';
$sessionManager = SessionManager::getInstance();
$isLoggedIn = $sessionManager->isLoggedIn();

// Get basket count if user is logged in
$basketCount = 0;
if ($isLoggedIn) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM basket_items WHERE user_id = ?");
    $stmt->execute([$sessionManager->getUserId()]);
    $result = $stmt->fetch();
    $basketCount = (int)($result['total'] ?? 0);

    // Get user's premium status
    $stmt = $pdo->prepare("SELECT is_premium FROM users WHERE id = ?");
    $stmt->execute([$sessionManager->getUserId()]);
    $user = $stmt->fetch();
    $isPremium = $user['is_premium'] ?? false;
}
?>
<header class="fixed top-0 left-0 right-0 bg-white dark:bg-gray-800 shadow z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="index.php" class="text-xl font-bold text-gray-800 dark:text-white">Sidestacker</a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="index.php" class="text-gray-900 dark:text-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="content.php" class="text-gray-900 dark:text-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Content</a>
                    <a href="tools.php" class="text-gray-900 dark:text-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Tools</a>
                    <a href="shop.php" class="text-gray-900 dark:text-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Shop</a>
                    <?php if ($isLoggedIn): ?>
                        <a href="dashboard.php" class="text-gray-900 dark:text-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-900 dark:text-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium inline-flex items-center">
                                Resources
                                <svg class="ml-2 h-4 w-4" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5"
                                 style="display: none;">
                                <div class="py-1">
                                    <a href="courses.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Courses
                                    </a>
                                    <a href="financial_offers.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Financial Offers
                                    </a>
                                    <a href="business_guides.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Business Guides
                                    </a>
                                    <a href="tools_software.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Tools & Software
                                    </a>
                                    <a href="queries.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Queries & Polls
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex items-center">
                <!-- Basket Icon -->
                <a href="basket.php" class="relative p-2 text-gray-900 dark:text-gray-100 hover:text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <?php if ($basketCount > 0): ?>
                        <span id="basket-count" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"><?php echo $basketCount; ?></span>
                    <?php endif; ?>
                </a>

                <?php if ($isLoggedIn): ?>
                    <?php if (!$isPremium): ?>
                        <a href="premium.php" class="ml-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:opacity-90 transition-opacity">
                            Upgrade to Premium
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="ml-4 text-gray-900 dark:text-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="ml-4 text-gray-900 dark:text-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<!-- Include Alpine.js -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Sidenav Menu -->
<div id="sidenav-menu" class="fixed inset-y-0 right-0 w-64 bg-white dark:bg-gray-800 shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50">
    <div class="p-6">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Menu</h2>
            <button onclick="toggleSidenav()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <nav class="space-y-4">
            <a href="index.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Home</a>
            <a href="content.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Content</a>
            <a href="tools.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Tools</a>
            <a href="shop.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Shop</a>
            <?php if ($isLoggedIn): ?>
                <a href="dashboard.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
                <a href="courses.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Courses</a>
                <a href="financial_offers.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Financial Offers</a>
                <a href="business_guides.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Business Guides</a>
                <a href="tools_software.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Tools & Software</a>
                <a href="queries.php" class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">Queries & Polls</a>
                <?php if (!$isPremium): ?>
                    <a href="premium.php" class="block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-semibold">Upgrade to Premium</a>
                <?php endif; ?>
                <a href="logout.php" class="block text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">Logout</a>
            <?php else: ?>
                <a href="login.php" class="block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">Login</a>
                <a href="register.php" class="block text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</div>

<script>
// Function to update basket count
function updateBasketCount() {
    fetch('/Sidestacker/get_basket_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const basketCountElement = document.getElementById('basket-count');
                if (data.basketCount > 0) {
                    if (!basketCountElement) {
                        const span = document.createElement('span');
                        span.id = 'basket-count';
                        span.className = 'absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full';
                        span.textContent = data.basketCount;
                        document.querySelector('a[href="basket.php"]').appendChild(span);
                    } else {
                        basketCountElement.textContent = data.basketCount;
                    }
                } else if (basketCountElement) {
                    basketCountElement.remove();
                }
            }
        });
}

// Update basket count every 30 seconds
setInterval(updateBasketCount, 30000);
</script>
