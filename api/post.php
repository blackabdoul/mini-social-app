<?php
require_once "config.php";
require_once "auth.php";

$method = $_SERVER['REQUEST_METHOD'];

// ── GET /api/post.php?id=X — fetch single post (public) ─────────
if ($method === 'GET') {

    $postId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

    if (!$postId) {
        sendResponse(400, ['error' => 'Valid post ID required']);
    }

    $stmt = $pdo->prepare("
        SELECT  p.id,
                p.content,
                p.image_path,
                p.created_at,
                p.updated_at,
                u.id        AS user_id,
                u.full_name AS author_name,
                u.email     AS author_email
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = :id
        LIMIT 1
    ");
    $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        sendResponse(404, ['error' => 'Post not found']);
    }

    sendResponse(200, ['post' => $post]);
}

// ── POST /api/post.php — create post (JWT required) ─────────────
elseif ($method === 'POST') {

    $currentUser = requireAuth();
    $userId      = $currentUser['user_id'];

    // multipart/form-data — content comes from $_POST, image from $_FILES
    $content   = trim($_POST['content'] ?? '');
    $imagePath = null;

    if (!$content && empty($_FILES['image']['name'])) {
        sendResponse(400, ['error' => 'Post must have content or an image']);
    }

    if (strlen($content) > 1000) {
        sendResponse(400, ['error' => 'Content exceeds 1000 characters']);
    }

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/posts/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($mimeType, $allowed)) {
            sendResponse(400, ['error' => 'Only JPG, PNG, GIF and WEBP images are allowed']);
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            sendResponse(400, ['error' => 'Image must be under 5MB']);
        }

        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('post_', true) . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
            sendResponse(500, ['error' => 'Image upload failed']);
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
        $newId = $pdo->lastInsertId();
        sendResponse(201, [
            'message' => 'Post created successfully',
            'post_id' => $newId
        ]);
    } else {
        sendResponse(500, ['error' => 'Failed to create post']);
    }
}

// ── DELETE /api/post.php?id=X — delete post (owner or admin) ────
elseif ($method === 'DELETE') {

    $currentUser = requireAuth();
    $postId      = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

    if (!$postId) {
        sendResponse(400, ['error' => 'Valid post ID required']);
    }

    // Fetch post to check ownership and get image path
    $stmt = $pdo->prepare("SELECT user_id, image_path FROM posts WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        sendResponse(404, ['error' => 'Post not found']);
    }

    $isSelf  = $currentUser['user_id'] == $post['user_id'];
    $isAdmin = $currentUser['role'] === 'admin';

    if (!$isSelf && !$isAdmin) {
        sendResponse(403, ['error' => 'You do not have permission to delete this post']);
    }

    $del = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $del->bindParam(':id', $postId, PDO::PARAM_INT);

    if ($del->execute()) {
        // Clean up image file from server
        if ($post['image_path'] && file_exists(__DIR__ . '/../' . $post['image_path'])) {
            unlink(__DIR__ . '/../' . $post['image_path']);
        }
        sendResponse(200, ['message' => 'Post deleted successfully']);
    } else {
        sendResponse(500, ['error' => 'Failed to delete post']);
    }
}

else {
    sendResponse(405, ['error' => 'Method not allowed']);
}
?>