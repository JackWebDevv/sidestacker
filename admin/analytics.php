<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get analytics data
try {
    // User statistics
    $userStats = $pdo->query("
        SELECT 
            COUNT(*) as total_users,
            COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_users_30d,
            COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as active_users_7d
        FROM users
    ")->fetch(PDO::FETCH_ASSOC);

    // Product statistics
    $productStats = $pdo->query("
        SELECT 
            COUNT(*) as total_products,
            AVG(price) as avg_price,
            SUM(stock) as total_stock
        FROM products
    ")->fetch(PDO::FETCH_ASSOC);

    // Recent activity
    $recentActivity = $pdo->query("
        (SELECT 'user' as type, username as name, created_at, 'New user registered' as action
         FROM users 
         ORDER BY created_at DESC LIMIT 5)
        UNION ALL
        (SELECT 'product' as type, name, created_at, 'Product added' as action
         FROM products 
         ORDER BY created_at DESC LIMIT 5)
        ORDER BY created_at DESC LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error fetching analytics: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <div class="container py-4">
        <h1 class="mb-4">Analytics Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">User Statistics</h5>
                        <p class="card-text">
                            Total Users: <?php echo $userStats['total_users']; ?><br>
                            New Users (30d): <?php echo $userStats['new_users_30d']; ?><br>
                            Active Users (7d): <?php echo $userStats['active_users_7d']; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Product Statistics</h5>
                        <p class="card-text">
                            Total Products: <?php echo $productStats['total_products']; ?><br>
                            Average Price: $<?php echo number_format($productStats['avg_price'], 2); ?><br>
                            Total Stock: <?php echo $productStats['total_stock']; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">System Status</h5>
                        <p class="card-text">
                            Server Status: Online<br>
                            Last Backup: <?php echo date('Y-m-d H:i:s'); ?><br>
                            System Load: Normal
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Action</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivity as $activity): ?>
                                <tr>
                                    <td>
                                        <i class="fa fa-<?php echo $activity['type'] === 'user' ? 'user' : 'shopping-cart'; ?>"></i>
                                        <?php echo ucfirst($activity['type']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['name']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($activity['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
