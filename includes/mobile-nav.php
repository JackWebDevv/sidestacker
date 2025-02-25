<!-- Mobile Navigation -->
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
