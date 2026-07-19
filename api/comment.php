<?php
require_once "config.php";
require_once "auth.php";

$method = $_SERVER['REQUEST_METHOD'];

// ── POST /api/comment.php — create comment (JWT required) ────────
if ($method === 'POST') {

    $currentUser = requireAuth();
    $userId      = $currentUser['user_id'];
    $data        = getRequestBody();

    $postId  = isset($data['post_id']) && is_numeric($data['post_id']) ? (int)$data['post_id'] : null;
    $content = trim($data['content'] ?? '');

    if (!$postId) {
        sendResponse(400, ['error' => 'Valid post_id required']);
    }

    if (!$content) {
        sendResponse(400, ['error' => 'Comment content cannot be empty']);
    }

    if (strlen($content) > 500) {
        sendResponse(400, ['error' => 'Comment exceeds 500 characters']);
    }

    // Verify post exists
    $check = $pdo->prepare("SELECT id FROM posts WHERE id = :id LIMIT 1");
    $check->bindParam(':id', $postId, PDO::PARAM_INT);
    $check->execute();
    if (!$check->fetch()) {
        sendResponse(404, ['error' => 'Post not found']);
    }

    $stmt = $pdo->prepare(
        "INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)"
    );
    $stmt->bindParam(':post_id', $postId,  PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId,  PDO::PARAM_INT);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $newId = $pdo->lastInsertId();
        sendResponse(201, [
            'message'    => 'Comment posted successfully',
            'comment_id' => $newId,
            'post_id'    => $postId
        ]);
    } else {
        sendResponse(500, ['error' => 'Failed to post comment']);
    }
}

// ── DELETE /api/comment.php?id=X — delete comment (owner or admin)
elseif ($method === 'DELETE') {

    $currentUser = requireAuth();
    $commentId   = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

    if (!$commentId) {
        sendResponse(400, ['error' => 'Valid comment ID required']);
    }

    // Fetch comment to verify ownership
    $stmt = $pdo->prepare("SELECT user_id, post_id FROM comments WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $commentId, PDO::PARAM_INT);
    $stmt->execute();
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment) {
        sendResponse(404, ['error' => 'Comment not found']);
    }

    $isSelf  = $currentUser['user_id'] == $comment['user_id'];
    $isAdmin = $currentUser['role'] === 'admin';

    if (!$isSelf && !$isAdmin) {
        sendResponse(403, ['error' => 'You do not have permission to delete this comment']);
    }

    $del = $pdo->prepare("DELETE FROM comments WHERE id = :id");
    $del->bindParam(':id', $commentId, PDO::PARAM_INT);

    if ($del->execute()) {
        sendResponse(200, [
            'message'    => 'Comment deleted successfully',
            'comment_id' => $commentId,
            'post_id'    => $comment['post_id']
        ]);
    } else {
        sendResponse(500, ['error' => 'Failed to delete comment']);
    }
}

else {
    sendResponse(405, ['error' => 'Method not allowed']);
}
?>