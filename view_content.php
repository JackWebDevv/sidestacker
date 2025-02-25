<?php
require_once 'includes/db_connect.php';
require_once 'includes/track_history.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: content.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM content WHERE slug = :slug AND status = 'published'");
$stmt->execute(['slug' => $slug]);
$content = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$content) {
    header('Location: content.php');
    exit();
}

// Track history if user is logged in
if (isset($_SESSION['user_id'])) {
    track_user_history($_SESSION['user_id'], 'content', $content['id'], 'view');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($content['title']); ?> - Sidestacker</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content-body img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
        }
        .content-body {
            line-height: 1.6;
        }
        pre {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            overflow-x: auto;
        }
        blockquote {
            border-left: 4px solid #dee2e6;
            padding-left: 1rem;
            margin-left: 0;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="content.php">Content</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($content['title']); ?></li>
            </ol>
        </nav>

        <article class="mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-<?php echo $content['content_type'] === 'blog' ? 'primary' : 'success'; ?> mb-2">
                    <?php echo ucfirst($content['content_type']); ?>
                </span>
                <small class="text-muted">
                    Published on <?php echo date('F j, Y', strtotime($content['created_at'])); ?>
                </small>
            </div>

            <h1 class="mb-4"><?php echo htmlspecialchars($content['title']); ?></h1>
            
            <?php if ($content['excerpt']): ?>
                <p class="lead mb-4"><?php echo htmlspecialchars($content['excerpt']); ?></p>
            <?php endif; ?>
            
            <?php if ($content['tags']): ?>
                <div class="mb-4">
                    <?php foreach (explode(',', $content['tags']) as $tag): ?>
                        <span class="badge bg-secondary me-1"><?php echo trim($tag); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="content-body mt-4">
                <?php echo $content['content_text']; ?>
            </div>

            <div class="mt-4 border-top pt-3">
                <p class="text-muted">Written by <?php echo htmlspecialchars($content['author']); ?></p>
            </div>
        </article>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
