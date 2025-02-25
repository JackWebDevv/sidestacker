<?php
require_once 'includes/db_connect.php';
require_once 'includes/auth_middleware.php';

// Get product slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Get product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND status = 'active'");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: shop.php');
    exit;
}

// Check if user is logged in
$isLoggedIn = false;
$isSaved = false;
try {
    $sessionManager = SessionManager::getInstance();
    $isLoggedIn = $sessionManager->isLoggedIn();
    if ($isLoggedIn) {
        $userId = $sessionManager->getUserId();
        // Check if product is saved
        $stmt = $pdo->prepare("SELECT id FROM saved_items WHERE user_id = ? AND item_id = ? AND item_type = 'product'");
        $stmt->execute([$userId, $product['id']]);
        $isSaved = (bool)$stmt->fetch();
    }
} catch (Exception $e) {
    // Handle error silently
}

$pageTitle = $product['name'];
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
        <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
            <!-- Image gallery -->
            <div class="flex flex-col">
                <div class="overflow-hidden rounded-lg">
                    <img src="https://picsum.photos/seed/<?php echo urlencode($product['slug']); ?>/800/600" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Product info -->
            <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>

                <div class="mt-3">
                    <h2 class="sr-only">Product information</h2>
                    <p class="text-3xl tracking-tight text-gray-900 dark:text-white">
                        $<?php echo number_format($product['price'], 2); ?>
                    </p>
                </div>

                <div class="mt-6">
                    <h3 class="sr-only">Description</h3>
                    <div class="space-y-6 text-base text-gray-700 dark:text-gray-300">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>
                </div>

                <div class="mt-6 flex items-center">
                    <?php if ($isLoggedIn): ?>
                        <button onclick="toggleSaveProduct(<?php echo $product['id']; ?>, this)"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm 
                                       <?php echo $isSaved ? 'text-red-600 bg-red-50 hover:bg-red-100' : 'text-gray-700 bg-gray-50 hover:bg-gray-100'; ?> 
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-heart mr-2"></i>
                            <span class="save-text"><?php echo $isSaved ? 'Saved' : 'Save for Later'; ?></span>
                        </button>
                    <?php else: ?>
                        <a href="login.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm 
                                                 text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Login to Save
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
                    button.querySelector('.save-text').textContent = 'Save for Later';
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
