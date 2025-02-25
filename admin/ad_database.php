<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle ad actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $ad_id = $_POST['ad_id'] ?? 0;
        
        switch ($_POST['action']) {
            case 'approve':
                $stmt = $pdo->prepare("UPDATE advertisements SET status = 'approved' WHERE id = ?");
                $stmt->execute([$ad_id]);
                break;
                
            case 'reject':
                $stmt = $pdo->prepare("UPDATE advertisements SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$ad_id]);
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM advertisements WHERE id = ?");
                $stmt->execute([$ad_id]);
                break;
        }
        
        header('Location: ad_database.php');
        exit();
    }
}

// Get advertisements with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$total_ads = $pdo->query("SELECT COUNT(*) FROM advertisements")->fetchColumn();
$total_pages = ceil($total_ads / $per_page);

$stmt = $pdo->prepare("SELECT a.*, u.username 
                     FROM advertisements a 
                     LEFT JOIN users u ON a.user_id = u.id 
                     ORDER BY a.created_at DESC 
                     LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$advertisements = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ad Database - Sidestacker Admin</title>
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
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="ad_database.php">Ad Database</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Advertisement Database</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>User</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($advertisements as $ad): ?>
                            <tr>
                                <td><?= htmlspecialchars($ad['id']) ?></td>
                                <td><?= htmlspecialchars($ad['title']) ?></td>
                                <td><?= htmlspecialchars($ad['username']) ?></td>
                                <td><?= htmlspecialchars($ad['ad_type']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $ad['status'] === 'approved' ? 'success' : 
                                        ($ad['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                                        <?= htmlspecialchars(ucfirst($ad['status'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($ad['created_at']) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if ($ad['status'] !== 'approved'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($ad['status'] !== 'rejected'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this advertisement?');">
                                            <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
