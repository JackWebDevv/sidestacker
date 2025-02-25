<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../includes/db_connect.php';

// Handle image upload
if (isset($_FILES['file'])) {  
    $response = array();
    try {
        $target_dir = "../uploads/";
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $fileExtension;
        $target_file = $target_dir . $newFileName;
        
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Check if image file is actual image
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        if($check === false) {
            throw new Exception('File is not an image.');
        }
        
        // Check file size (5MB max)
        if ($_FILES["file"]["size"] > 5000000) {
            throw new Exception('File is too large. Maximum size is 5MB.');
        }
        
        // Allow certain file formats
        if(!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed.');
        }
        
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $response = array(
                'location' => '/Sidestacker/uploads/' . $newFileName
            );
        } else {
            throw new Exception('Failed to upload file.');
        }
        
    } catch (Exception $e) {
        $response = array(
            'error' => $e->getMessage()
        );
        http_response_code(500);
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['file'])) {
    try {
        // Create base slug
        $base_slug = strtolower(str_replace(' ', '-', $_POST['title']));
        $base_slug = preg_replace('/[^A-Za-z0-9\-]/', '', $base_slug);
        
        // Check if slug exists and generate unique one if needed
        $slug = $base_slug;
        $counter = 1;
        
        do {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM content WHERE slug = :slug");
            $stmt->execute(['slug' => $slug]);
            $exists = $stmt->fetchColumn();
            
            if ($exists) {
                $slug = $base_slug . '-' . $counter;
                $counter++;
            }
        } while ($exists);
        
        $stmt = $pdo->prepare("INSERT INTO content (title, content_type, content_text, excerpt, author, status, slug, meta_description, tags) 
                              VALUES (:title, :content_type, :content_text, :excerpt, :author, :status, :slug, :meta_description, :tags)");
        
        $stmt->execute([
            'title' => $_POST['title'],
            'content_type' => $_POST['content_type'],
            'content_text' => $_POST['content_text'],
            'excerpt' => $_POST['excerpt'],
            'author' => $_POST['author'],
            'status' => $_POST['status'],
            'slug' => $slug,
            'meta_description' => $_POST['meta_description'],
            'tags' => $_POST['tags']
        ]);
        
        $_SESSION['message'] = "Content created successfully! View it below.";
        header('Location: ../content.php');
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get messages from session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// Clear session messages
unset($_SESSION['message']);
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Content - Sidestacker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/0xtghypwg7t6hfueyu2ecd0pjzp78nyqjiyxdwbarg7e4l9q/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        .tox-tinymce {
            min-height: 500px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Create New Content</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-4" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            
            <div class="mb-3">
                <label for="content_type" class="form-label">Content Type</label>
                <select class="form-control" id="content_type" name="content_type" required>
                    <option value="blog">Blog Post</option>
                    <option value="tutorial">Tutorial</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="content_text" class="form-label">Content</label>
                <textarea class="form-control" id="content_text" name="content_text"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="excerpt" class="form-label">Excerpt</label>
                <textarea class="form-control" id="excerpt" name="excerpt" rows="3"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" id="author" name="author">
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="meta_description" class="form-label">Meta Description</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="2"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="tags" class="form-label">Tags (comma-separated)</label>
                <input type="text" class="form-control" id="tags" name="tags">
            </div>
            
            <button type="submit" class="btn btn-primary">Create Content</button>
            <a href="content.php" class="btn btn-secondary">View All Content</a>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        tinymce.init({
            selector: '#content_text',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            images_upload_url: 'create_content.php',
            automatic_uploads: true,
            images_upload_credentials: true,
            images_reuse_filename: false,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            image_uploadtab: true,
            file_picker_types: 'image',
            required: true,
            setup: function(editor) {
                editor.on('init', function() {
                    editor.notificationManager.open({
                        text: 'Editor ready for image uploads',
                        type: 'info',
                        timeout: 3000
                    });
                });
            },
            init_instance_callback: function(editor) {
                editor.on('Change', function(e) {
                    // Update hidden textarea with editor content
                    editor.save();
                });
            },
            images_upload_handler: function (blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', 'create_content.php');

                    xhr.upload.onprogress = (e) => {
                        progress(e.loaded / e.total * 100);
                    };

                    xhr.onload = function() {
                        if (xhr.status === 403) {
                            reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                            return;
                        }
                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }
                        let json;
                        try {
                            json = JSON.parse(xhr.responseText);
                        } catch (e) {
                            reject('Invalid JSON: ' + xhr.responseText);
                            return;
                        }
                        if (!json || typeof json.location != 'string') {
                            reject('Invalid JSON: ' + xhr.responseText);
                            return;
                        }
                        resolve(json.location);
                    };

                    xhr.onerror = () => {
                        reject('Image upload failed due to a network error');
                    };

                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());

                    xhr.send(formData);
                });
            },
            image_dimensions: true,
            image_class_list: [
                {title: 'Responsive', value: 'img-fluid'}
            ],
            content_style: 'img { max-width: 100%; height: auto; }'
        });

        // Add form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const editor = tinymce.get('content_text');
            const content = editor.getContent();
            
            if (!content.trim()) {
                e.preventDefault();
                editor.notificationManager.open({
                    text: 'Please enter some content before submitting.',
                    type: 'error',
                    timeout: 3000
                });
                return false;
            }
            
            // Update textarea with current content before submit
            editor.save();
            return true;
        });
    </script>
</body>
</html>
