<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle tool operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $pdo->prepare("INSERT INTO tools (name, description, category, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['name'], $_POST['description'], $_POST['category'], $_POST['status']]);
                break;
            case 'update':
                $stmt = $pdo->prepare("UPDATE tools SET name = ?, description = ?, category = ?, status = ? WHERE id = ?");
                $stmt->execute([$_POST['name'], $_POST['description'], $_POST['category'], $_POST['status'], $_POST['id']]);
                break;
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM tools WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                break;
        }
    }
}

// Get all tools
$tools = [];
try {
    $stmt = $pdo->query("SELECT * FROM tools ORDER BY category, name");
    $tools = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching tools: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tools - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Tools</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addToolModal">
                <i class="fa fa-plus"></i> Add New Tool
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tools as $tool): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tool['name']); ?></td>
                            <td><?php echo htmlspecialchars($tool['category']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $tool['status'] === 'active' ? 'success' : 'warning'; ?>">
                                    <?php echo htmlspecialchars($tool['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editTool(<?php echo $tool['id']; ?>)">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteTool(<?php echo $tool['id']; ?>)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Tool Modal -->
    <div class="modal fade" id="addToolModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Tool</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addToolForm" method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category" required>
                                <option value="hand_tools">Hand Tools</option>
                                <option value="power_tools">Power Tools</option>
                                <option value="measurement">Measurement</option>
                                <option value="safety">Safety Equipment</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addToolForm" class="btn btn-primary">Add Tool</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editTool(id) {
            // Implement edit functionality
        }

        function deleteTool(id) {
            if (confirm('Are you sure you want to delete this tool?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
