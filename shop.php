<?php
require_once 'includes/db_connect.php';
require_once 'includes/auth_middleware.php';

// Get all active products
$stmt = $pdo->prepare("SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC");
$stmt->execute();
$products = $stmt->fetchAll();

// Check if user is logged in
$isLoggedIn = false;
$savedProducts = [];
try {
    $sessionManager = SessionManager::getInstance();
    $isLoggedIn = $sessionManager->isLoggedIn();
    if ($isLoggedIn) {
        $userId = $sessionManager->getUserId();
        // Get user's saved products
        $saved_stmt = $pdo->prepare("SELECT item_id FROM saved_items WHERE user_id = ? AND item_type = 'product'");
        $saved_stmt->execute([$userId]);
        $savedProducts = array_column($saved_stmt->fetchAll(), 'item_id');
    }
} catch (Exception $e) {
    // Handle error silently
}

$pageTitle = "Shop";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Sidestacker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <?php include 'includes/header.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 mt-20">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Shop</h1>

        <?php if (empty($products)): ?>
            <p class="text-gray-500 dark:text-gray-400">No products available.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <!-- Product Image -->
                        <a href="product.php?slug=<?php echo urlencode($product['slug']); ?>" class="block">
                            <img src="https://picsum.photos/seed/<?php echo urlencode($product['slug']); ?>/400/300" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 class="w-full h-48 object-cover">
                        </a>

                        <div class="p-4">
                            <!-- Product Name -->
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <a href="product.php?slug=<?php echo urlencode($product['slug']); ?>" 
                                   class="hover:text-blue-600 dark:hover:text-blue-400">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h3>

                            <!-- Product Description -->
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
                            </p>

                            <!-- Price and Save Button -->
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    $<?php echo number_format($product['price'], 2); ?>
                                </span>
                                
                                <?php if ($isLoggedIn): ?>
                                    <button onclick="toggleSaveProduct(<?php echo $product['id']; ?>, this)"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm 
                                                   <?php echo in_array($product['id'], $savedProducts) ? 'text-red-600 bg-red-50 hover:bg-red-100' : 'text-gray-700 bg-gray-50 hover:bg-gray-100'; ?> 
                                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-heart mr-1"></i>
                                        <span class="save-text"><?php echo in_array($product['id'], $savedProducts) ? 'Saved' : 'Save'; ?></span>
                                    </button>
                                <?php else: ?>
                                    <a href="login.php" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm 
                                                            text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Login to Save
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
    async function toggleSaveProduct(productId, button) {
        try {
            const isSaved = button.querySelector('.save-text').textContent === 'Saved';
            const action = isSaved ? 'unsave' : 'save';

            const response = await fetch('save_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&action=${action}`
            });

            const data = await response.json();
            if (data.success) {
                // Update button appearance
                if (action === 'save') {
                    button.classList.remove('text-gray-700', 'bg-gray-50', 'hover:bg-gray-100');
                    button.classList.add('text-red-600', 'bg-red-50', 'hover:bg-red-100');
                    button.querySelector('.save-text').textContent = 'Saved';
                } else {
                    button.classList.remove('text-red-600', 'bg-red-50', 'hover:bg-red-100');
                    button.classList.add('text-gray-700', 'bg-gray-50', 'hover:bg-gray-100');
                    button.querySelector('.save-text').textContent = 'Save';
                }
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while saving the product');
        }
    }
    </script>
</body>
</html>
