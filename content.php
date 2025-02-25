<?php
require_once 'includes/db_connect.php';
require_once 'includes/session_manager.php';
require_once 'includes/track_history.php';

$sessionManager = SessionManager::getInstance();

// Track view if content slug is provided
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    
    // Get content ID
    $content_stmt = $pdo->prepare("SELECT id FROM content WHERE slug = ? AND status = 'published' LIMIT 1");
    $content_stmt->execute([$slug]);
    $content = $content_stmt->fetch();
    
    if ($content && $sessionManager->isLoggedIn()) {
        track_user_history($sessionManager->getUserId(), 'content', $content['id'], 'view');
    }
}

// Get content type filter if specified
$content_type = isset($_GET['type']) ? $_GET['type'] : null;

// Prepare the query
$query = "SELECT * FROM content WHERE status = 'published'";
if ($content_type) {
    $query .= " AND content_type = :content_type";
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
if ($content_type) {
    $stmt->bindParam(':content_type', $content_type);
}
$stmt->execute();
$contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get flash message if any
$flashMessage = $sessionManager->getFlashMessage();

// Set page title
$pageTitle = 'Content';

// Add any additional styles
$additionalStyles = '
    .content-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .content-card:hover {
        transform: translateY(-5px);
    }
';

// Include header
include 'includes/head.php';
?>

    <?php include 'includes/header.php'; ?>

    <div class="max-w-7xl mx-auto px-4 mt-20 mb-20">
        <?php if ($flashMessage): ?>
            <div class="rounded-md bg-<?php echo $flashMessage['type'] === 'success' ? 'green' : 'red'; ?>-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-<?php echo $flashMessage['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?> text-<?php echo $flashMessage['type'] === 'success' ? 'green' : 'red'; ?>-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-<?php echo $flashMessage['type'] === 'success' ? 'green' : 'red'; ?>-800">
                            <?php echo htmlspecialchars($flashMessage['message']); ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Top AdSense -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6 text-center">
            <!-- Google AdSense -->
            <div class="h-[90px] bg-gray-50 flex items-center justify-center">
                <span class="text-gray-400">Advertisement Space</span>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="flex-1">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold font-heading text-gray-900 dark:text-white">Latest Content</h1>
                    <div class="inline-flex rounded-md shadow-sm">
                        <a href="content.php" class="<?php echo !$content_type ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'; ?> px-4 py-2 text-sm font-medium rounded-l-lg border hover:bg-gray-50">All</a>
                        <a href="content.php?type=blog" class="<?php echo $content_type === 'blog' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'; ?> px-4 py-2 text-sm font-medium border-t border-b border-r hover:bg-gray-50">Blog Posts</a>
                        <a href="content.php?type=tutorial" class="<?php echo $content_type === 'tutorial' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'; ?> px-4 py-2 text-sm font-medium rounded-r-lg border hover:bg-gray-50">Tutorials</a>
                    </div>
                </div>

                <?php if (empty($contents)): ?>
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    No content available yet.
                                    <?php if ($sessionManager->getRole() === 'admin'): ?>
                                        <a href="admin/create_content.php" class="font-medium underline">Create your first post!</a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($contents as $content): ?>
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm content-card">
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-2">
                                                <?php echo htmlspecialchars(ucfirst($content['content_type'])); ?>
                                            </span>
                                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                                <a href="view_content.php?slug=<?php echo urlencode($content['slug']); ?>" class="hover:text-blue-600">
                                                    <?php echo htmlspecialchars($content['title']); ?>
                                                </a>
                                            </h2>
                                        </div>
                                        <?php if ($sessionManager->isLoggedIn()): ?>
                                            <?php
                                            $saved_check = $pdo->prepare("SELECT id FROM saved_items WHERE user_id = ? AND item_type = 'content' AND item_id = ?");
                                            $saved_check->execute([$sessionManager->getUserId(), $content['id']]);
                                            $is_saved = $saved_check->fetch();
                                            ?>
                                            <button 
                                                onclick="toggleSave('content', <?php echo $content['id']; ?>)"
                                                class="save-button text-gray-500 hover:text-blue-600"
                                                data-item-id="<?php echo $content['id']; ?>"
                                                data-item-type="content"
                                            >
                                                <i class="fas fa-bookmark <?php echo $is_saved ? 'text-blue-600' : ''; ?>"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300 mb-4"><?php echo htmlspecialchars($content['excerpt']); ?></p>
                                    <?php if ($content['tags']): ?>
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <?php foreach (explode(',', $content['tags']) as $tag): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    <i class="fas fa-tag mr-1"></i><?php echo trim($tag); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-user mr-1"></i>By <?php echo htmlspecialchars($content['author']); ?>
                                        </span>
                                        <a href="view_content.php?slug=<?php echo $content['slug']; ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                            Read More <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 space-y-6">
                <!-- Sidebar AdSense -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <!-- Google AdSense -->
                    <div class="h-[250px] bg-gray-50 dark:bg-gray-700 flex items-center justify-center">
                        <span class="text-gray-400">Advertisement Space</span>
                    </div>
                </div>

                <?php if ($sessionManager->getRole() === 'admin'): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-4">Admin Actions</h3>
                        <a href="admin/create_content.php" class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <i class="fas fa-plus mr-2"></i>Create New Content
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Another Sidebar AdSense -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <!-- Google AdSense -->
                    <div class="h-[250px] bg-gray-50 dark:bg-gray-700 flex items-center justify-center">
                        <span class="text-gray-400 dark:text-gray-500">Advertisement Space</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="js/save-items.js"></script>
</body>
</html>
