<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit();
}

$action = $_POST['action'] ?? '';
$userId = $_SESSION['id_user'];

// ── CREATE POST ──────────────────────────────────────────────────
if ($action === 'create_post') {

    $content = trim($_POST['content'] ?? '');
    $imagePath = null;

    // Validate — need at least content or an image
    if (!$content && empty($_FILES['image']['name'])) {
        $_SESSION['error'] = "Post cannot be empty.";
        header("Location: dashboard.php");
        exit();
    }

    if (strlen($content) > 1000) {
        $_SESSION['error'] = "Post exceeds 1000 characters.";
        header("Location: dashboard.php");
        exit();
    }

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/uploads/posts/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowed   = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType  = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($mimeType, $allowed)) {
            $_SESSION['error'] = "Only JPG, PNG, GIF and WEBP images are allowed.";
            header("Location: dashboard.php");
            exit();
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "Image must be under 5MB.";
            header("Location: dashboard.php");
            exit();
        }

        $ext       = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename  = uniqid('post_', true) . '.' . $ext;
        $destPath  = $uploadDir . $filename;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
            $_SESSION['error'] = "Image upload failed. Please try again.";
            header("Location: dashboard.php");
            exit();
        }

        $imagePath = 'uploads/posts/' . $filename;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO posts (user_id, content, image_path) VALUES (:user_id, :content, :image_path)"
    );
    $stmt->bindParam(':user_id',    $userId,    PDO::PARAM_INT);
    $stmt->bindParam(':content',    $content,   PDO::PARAM_STR);
    $stmt->bindParam(':image_path', $imagePath, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Post created successfully.";
    } else {
        $_SESSION['error'] = "Failed to create post. Please try again.";
    }

    header("Location: dashboard.php");
    exit();
}

// DELETE 
if ($action === 'delete_post') {

    $postId = (int)($_POST['post_id'] ?? 0);

    if (!$postId) {
        $_SESSION['error'] = "Invalid post.";
        header("Location: dashboard.php");
        exit();
    }

    // Fetch post to check ownership and get image path
    $stmt = $pdo->prepare("SELECT user_id, image_path FROM posts WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        $_SESSION['error'] = "Post not found.";
        header("Location: dashboard.php");
        exit();
    }

    // Only owner or admin can delete
    if ($post['user_id'] !== $userId && ($_SESSION['user_role'] ?? '') !== 'admin') {
        $_SESSION['error'] = "You don't have permission to delete this post.";
        header("Location: dashboard.php");
        exit();
    }

    $del = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $del->bindParam(':id', $postId, PDO::PARAM_INT);

    if ($del->execute()) {
        // Clean up uploaded image if it exists
        if ($post['image_path'] && file_exists(__DIR__ . '/' . $post['image_path'])) {
            unlink(__DIR__ . '/' . $post['image_path']);
        }
        $_SESSION['success'] = "Post deleted.";
    } else {
        $_SESSION['error'] = "Failed to delete post.";
    }

    header("Location: dashboard.php");
    exit();
}

// Unknown action
header("Location: dashboard.php");
exit();