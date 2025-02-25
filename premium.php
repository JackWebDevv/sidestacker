<?php
session_start();
require_once 'includes/db_connect.php';

// Process premium upgrade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upgrade'])) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $premium_until = date('Y-m-d', strtotime('+1 month'));
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET is_premium = TRUE, premium_until = ? WHERE id = ?");
        $stmt->execute([$premium_until, $user_id]);
        
        $_SESSION['flash_message'] = "Successfully upgraded to premium!";
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error processing upgrade. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium - Sidestacker</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#0070F3',
                        accent: '#7928CA',
                    },
                    fontFamily: {
                        clash: ['Clash Display', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <?php include 'includes/header.php'; ?>

    <div class="mt-24 max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-clash font-bold mb-4 bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
                Upgrade to Premium
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">
                Unlock the full potential of Sidestacker
            </p>
        </div>

        <?php if (isset($error)): ?>
        <div class="mb-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8">
                <div class="flex items-baseline justify-center mb-8">
                    <span class="text-5xl font-bold text-gray-900 dark:text-white">£7</span>
                    <span class="text-gray-600 dark:text-gray-400 ml-2">/month</span>
                </div>

                <div class="space-y-6">
                    <!-- Feature List -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Post job listings</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Access premium business plans</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Post advertisements</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Access premium tools</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">No restrictions on saved items</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Priority support</span>
                        </div>
                    </div>

                    <form method="POST" class="mt-8">
                        <button type="submit" name="upgrade" class="w-full bg-gradient-to-r from-primary to-accent text-white font-semibold py-3 px-6 rounded-lg hover:opacity-90 transition-opacity">
                            Upgrade Now
                        </button>
                    </form>

                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-4">
                        Secure payment processing. Cancel anytime.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
