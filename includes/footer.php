<!-- Bottom Navigation -->
<div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t dark:border-gray-800 py-2 px-4 z-50">
    <div class="max-w-lg mx-auto">
        <nav class="grid grid-cols-5 gap-2">
            <!-- Home -->
            <a href="index.php" class="flex flex-col items-center justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-xs mt-1 text-gray-600 dark:text-gray-400">Home</span>
            </a>

            <!-- Content -->
            <a href="content.php" class="flex flex-col items-center justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0015.5 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2z"/>
                </svg>
                <span class="text-xs mt-1 text-gray-600 dark:text-gray-400">Content</span>
            </a>

            <!-- Tools -->
            <a href="tools.php" class="flex flex-col items-center justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-xs mt-1 text-gray-600 dark:text-gray-400">Tools</span>
            </a>

            <!-- Shop -->
            <a href="shop.php" class="flex flex-col items-center justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span class="text-xs mt-1 text-gray-600 dark:text-gray-400">Shop</span>
            </a>

            <!-- Menu -->
            <button onclick="toggleSidenav()" class="flex flex-col items-center justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
                <span class="text-xs mt-1 text-gray-600 dark:text-gray-400">Menu</span>
            </button>
        </nav>
    </div>
</div>

<!-- Add bottom padding to account for fixed bottom nav -->
<div class="h-20"></div>

<!-- Initialize sidenav if not already initialized -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.toggleSidenav !== 'function') {
        console.log('Initializing fallback toggle in footer');
        window.toggleSidenav = function() {
            console.log('Footer fallback toggle called');
            const sidenav = document.getElementById('sidenav-menu');
            if (sidenav) {
                sidenav.classList.toggle('translate-x-full');
                sidenav.classList.toggle('translate-x-0');
            }
        };
    }
});
</script>

</body>
</html>
