<?php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

$postId = isset($_GET['post_id']) && is_numeric($_GET['post_id']) ? (int)$_GET['post_id'] : null;

if (!$postId) {
    sendResponse(400, ['error' => 'Valid post_id required']);
}

// Verify post exists
$check = $pdo->prepare("SELECT id FROM posts WHERE id = :id LIMIT 1");
$check->bindParam(':id', $postId, PDO::PARAM_INT);
$check->execute();
if (!$check->fetch()) {
    sendResponse(404, ['error' => 'Post not found']);
}

$stmt = $pdo->prepare("
    SELECT c.id, c.post_id, c.content, c.created_at,
           u.id        AS user_id,
           u.full_name AS author_name,
           u.email     AS author_email
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.post_id = :post_id
    ORDER BY c.created_at ASC
");
$stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total count
$count = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = :post_id");
$count->bindParam(':post_id', $postId, PDO::PARAM_INT);
$count->execute();

sendResponse(200, [
    'post_id'       => $postId,
    'comment_count' => (int)$count->fetchColumn(),
    'comments'      => $comments
]);
?>