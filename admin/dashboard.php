<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once '../includes/db_connect.php';

// Get statistics
try {
    // User stats
    $userStats = $pdo->query("SELECT COUNT(*) as total FROM users")->fetchColumn();
    
    // Content stats
    $contentStats = $pdo->query("SELECT COUNT(*) as total FROM content")->fetchColumn();
    
    // Product stats
    $productStats = $pdo->query("SELECT COUNT(*) as total FROM products")->fetchColumn();
    
    // Tool stats
    $toolStats = $pdo->query("SELECT COUNT(*) as total FROM tools")->fetchColumn();
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sidestacker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Sidestacker Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_content.php">Content</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_tools.php">Tools</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">Analytics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Users</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h1 class="mb-4">Dashboard</h1>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Users</h6>
                                <h2 class="mb-0"><?php echo $userStats; ?></h2>
                            </div>
                            <i class="fa fa-users fa-2x opacity-50"></i>
                        </div>
                        <a href="users.php" class="text-white text-decoration-none small">View Details →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Content</h6>
                                <h2 class="mb-0"><?php echo $contentStats; ?></h2>
                            </div>
                            <i class="fa fa-file-text fa-2x opacity-50"></i>
                        </div>
                        <a href="manage_content.php" class="text-white text-decoration-none small">Manage Content →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Products</h6>
                                <h2 class="mb-0"><?php echo $productStats; ?></h2>
                            </div>
                            <i class="fa fa-shopping-cart fa-2x opacity-50"></i>
                        </div>
                        <a href="manage_products.php" class="text-white text-decoration-none small">Manage Products →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Tools</h6>
                                <h2 class="mb-0"><?php echo $toolStats; ?></h2>
                            </div>
                            <i class="fa fa-wrench fa-2x opacity-50"></i>
                        </div>
                        <a href="manage_tools.php" class="text-white text-decoration-none small">Manage Tools →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="manage_content.php?action=new" class="btn btn-outline-primary">
                                <i class="fa fa-plus-circle"></i> Add New Content
                            </a>
                            <a href="manage_products.php?action=new" class="btn btn-outline-success">
                                <i class="fa fa-plus-circle"></i> Add New Product
                            </a>
                            <a href="manage_tools.php?action=new" class="btn btn-outline-warning">
                                <i class="fa fa-plus-circle"></i> Add New Tool
                            </a>
                            <a href="users.php?action=new" class="btn btn-outline-info">
                                <i class="fa fa-plus-circle"></i> Add New User
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Status</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Server Status
                                <span class="badge bg-success rounded-pill">Online</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Database
                                <span class="badge bg-success rounded-pill">Connected</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Last Backup
                                <span class="text-muted"><?php echo date('Y-m-d H:i'); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                PHP Version
                                <span class="text-muted"><?php echo phpversion(); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
