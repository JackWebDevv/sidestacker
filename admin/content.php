<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle content actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $content_id = $_POST['content_id'] ?? 0;
        
        switch ($_POST['action']) {
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
                $stmt->execute([$content_id]);
                break;
                
            case 'toggle_status':
                $stmt = $pdo->prepare("UPDATE content SET status = CASE WHEN status = 'published' THEN 'draft' ELSE 'published' END WHERE id = ?");
                $stmt->execute([$content_id]);
                break;
        }
        
        header('Location: content.php');
        exit();
    }
}

// Get content with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$total_content = $pdo->query("SELECT COUNT(*) FROM content")->fetchColumn();
$total_pages = ceil($total_content / $per_page);

$stmt = $pdo->prepare("SELECT * FROM content ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$contents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management - Sidestacker Admin</title>
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
                        <a class="nav-link active" href="content.php">Content</a>
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
            <h1>Content Management</h1>
            <a href="create_content.php" class="btn btn-primary">
                <i class="fa fa-plus"></i> Create New Content
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contents as $content): ?>
                                <tr>
                                    <td><?php echo $content['id']; ?></td>
                                    <td>
                                        <a href="../view_content.php?slug=<?php echo $content['slug']; ?>">
                                            <?php echo htmlspecialchars($content['title']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($content['username']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $content['content_type'] === 'blog' ? 'primary' : 'success'; ?>">
                                            <?php echo ucfirst($content['content_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $content['status'] === 'published' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($content['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($content['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="content_id" value="<?php echo $content['id']; ?>">
                                            <a href="edit_content.php?id=<?php echo $content['id']; ?>" class="btn btn-sm btn-primary">
                                                Edit
                                            </a>
                                            <button type="submit" name="action" value="toggle_status" class="btn btn-sm btn-warning">
                                                Toggle Status
                                            </button>
                                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this content?')">
                                                Delete
                                            </button>
                                        </form>
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
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
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
