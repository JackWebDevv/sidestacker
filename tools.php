<?php
require_once 'includes/db_connect.php';
require_once 'includes/auth_middleware.php';
require_once 'includes/session_manager.php';
require_once 'includes/track_history.php';

// Get session manager and user ID (or null if not logged in)
$sessionManager = SessionManager::getInstance();
$userId = $sessionManager->isLoggedIn() ? $sessionManager->getUserId() : null;

// Track tool usage if slug is provided
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    
    // Get tool ID
    $tool_stmt = $pdo->prepare("SELECT id FROM tools WHERE slug = ? LIMIT 1");
    $tool_stmt->execute([$slug]);
    $tool = $tool_stmt->fetch();
    
    if ($tool && $sessionManager->isLoggedIn()) {
        track_user_history($sessionManager->getUserId(), 'tool', $tool['id'], 'use');
        
        // Update tool usage count
        $update_stmt = $pdo->prepare("UPDATE tools SET usage_count = usage_count + 1 WHERE id = ?");
        $update_stmt->execute([$tool['id']]);
    }
}

?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tools - Sidestacker</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <div class="mt-24 max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-clash font-bold mb-4 bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
                Tools & Resources
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">
                Everything you need to grow your business and career
            </p>
        </div>

        <?php
        // Get filter parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $type = isset($_GET['type']) ? $_GET['type'] : 'all';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
        $view = isset($_GET['view']) ? $_GET['view'] : 'card';
        $category = isset($_GET['category']) ? $_GET['category'] : 'all';

        // Build the SQL query
        $sql = "SELECT * FROM tools WHERE 1=1";

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
        }

        if ($type !== 'all') {
            $sql .= " AND type = ?";
        }

        if ($category !== 'all') {
            $sql .= " AND category = ?";
        }

        // Add sorting
        switch ($sort) {
            case 'newest':
                $sql .= " ORDER BY created_at DESC";
                break;
            case 'name':
                $sql .= " ORDER BY name ASC";
                break;
            case 'popular':
            default:
                $sql .= " ORDER BY sort_order ASC, name ASC";
                break;
        }

        try {
            $stmt = $pdo->prepare($sql);
            
            // Bind parameters
            $paramIndex = 1;
            if (!empty($search)) {
                $searchParam = "%$search%";
                $stmt->bindValue($paramIndex++, $searchParam);
                $stmt->bindValue($paramIndex++, $searchParam);
            }
            if ($type !== 'all') {
                $stmt->bindValue($paramIndex++, $type);
            }
            if ($category !== 'all') {
                $stmt->bindValue($paramIndex++, $category);
            }
            
            $stmt->execute();
            $tools = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get unique categories for filter
            $categoryStmt = $pdo->query("SELECT DISTINCT category FROM tools ORDER BY category");
            $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $tools = [];
            $categories = [];
        }
        ?>

        <!-- Debug info -->
        <?php if ($sessionManager->isLoggedIn()): ?>
            <div class="bg-blue-100 p-4 mb-4 rounded">
                <p>Debug: User is logged in. User ID: <?php echo $userId; ?></p>
            </div>
        <?php else: ?>
            <div class="bg-yellow-100 p-4 mb-4 rounded">
                <p>Debug: User is not logged in</p>
            </div>
        <?php endif; ?>

        <!-- Filters Section -->
        <div class="mb-8 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 items-center">
                <!-- Search Bar -->
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Search tools..." 
                           class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <span class="absolute left-3 top-2.5">
                        <i class="fas fa-search text-gray-400"></i>
                    </span>
                </div>

                <!-- Category Filter -->
                <select name="category" class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <option value="all" <?php echo $category === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Type Filter -->
                <select name="type" class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <option value="all" <?php echo $type === 'all' ? 'selected' : ''; ?>>All Types</option>
                    <option value="free" <?php echo $type === 'free' ? 'selected' : ''; ?>>Free</option>
                    <option value="premium" <?php echo $type === 'premium' ? 'selected' : ''; ?>>Premium</option>
                </select>

                <!-- Sort Filter -->
                <select name="sort" class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name</option>
                </select>
            </div>

            <!-- View Toggle -->
            <div class="flex gap-2">
                <button onclick="setView('card')" class="px-4 py-2 rounded-lg <?php echo $view === 'card' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700'; ?>">
                    <i class="fas fa-th-large"></i>
                </button>
                <button onclick="setView('table')" class="px-4 py-2 rounded-lg <?php echo $view === 'table' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700'; ?>">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Tools Display -->
        <?php if ($view === 'card'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($tools as $tool): ?>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-2xl text-blue-500 dark:text-blue-400">
                                <i class="fas <?php echo htmlspecialchars($tool['icon_class']); ?>"></i>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-sm rounded-full <?php echo $tool['type'] === 'premium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($tool['type'])); ?>
                                </span>
                                <?php if ($sessionManager->isLoggedIn()): ?>
                                    <?php
                                    // Check if tool is saved
                                    $saved_check = $pdo->prepare("SELECT id FROM saved_items WHERE user_id = ? AND item_type = 'tool' AND item_id = ?");
                                    $saved_check->execute([$userId, $tool['id']]);
                                    $is_saved = $saved_check->fetch() !== false;
                                    ?>
                                    <button 
                                        class="save-tool-btn <?php echo $is_saved ? 'text-yellow-400' : 'text-gray-400'; ?> hover:text-yellow-500" 
                                        data-tool-id="<?php echo $tool['id']; ?>"
                                        title="<?php echo $is_saved ? 'Unsave tool' : 'Save tool'; ?>"
                                    >
                                        <i class="fas fa-star"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($tool['name']); ?></h3>
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
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tool</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($tools as $tool): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i class="fas <?php echo htmlspecialchars($tool['icon_class']); ?> text-2xl text-blue-500 dark:text-blue-400 mr-3"></i>
                                        <span class="font-medium"><?php echo htmlspecialchars($tool['name']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($tool['category']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-sm rounded-full <?php echo $tool['type'] === 'premium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($tool['type'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($tool['description']); ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <?php if ($sessionManager->isLoggedIn()): ?>
                                            <?php
                                            // Check if tool is saved
                                            $saved_check = $pdo->prepare("SELECT id FROM saved_items WHERE user_id = ? AND item_type = 'tool' AND item_id = ?");
                                            $saved_check->execute([$userId, $tool['id']]);
                                            $is_saved = $saved_check->fetch() !== false;
                                            ?>
                                            <button 
                                                class="save-tool-btn <?php echo $is_saved ? 'text-yellow-400' : 'text-gray-400'; ?> hover:text-yellow-500" 
                                                data-tool-id="<?php echo $tool['id']; ?>"
                                                title="<?php echo $is_saved ? 'Unsave tool' : 'Save tool'; ?>"
                                            >
                                                <i class="fas fa-star"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="/tool/<?php echo htmlspecialchars($tool['slug']); ?>" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300">
                                            Use Tool <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <script>
        function setView(viewType) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('view', viewType);
            window.location.search = urlParams.toString();
        }

        // Add save tool functionality
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
        </script>

        <script>
        // Make filters work automatically on change
        document.querySelectorAll('select, input[type="text"]').forEach(element => {
            element.addEventListener('change', function() {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set(this.name, this.value);
                window.location.search = urlParams.toString();
            });
        });

        // Add search on enter key
        document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set(this.name, this.value);
                window.location.search = urlParams.toString();
            }
        });
        </script>

    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
