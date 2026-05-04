<?php
require_once "config.php";
require_once "auth.php";

$method = $_SERVER['REQUEST_METHOD'];

// ── GET /api/like.php?post_id=X — like count + liked status ─────
if ($method === 'GET') {

    $currentUser = requireAuth();
    $userId      = $currentUser['user_id'];
    $postId      = isset($_GET['post_id']) && is_numeric($_GET['post_id']) ? (int)$_GET['post_id'] : null;

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

    // Get like count and whether current user liked it
    $stmt = $pdo->prepare("
        SELECT COUNT(*)                                          AS like_count,
               MAX(CASE WHEN user_id = :me THEN 1 ELSE 0 END)  AS liked_by_me
        FROM likes
        WHERE post_id = :post_id
    ");
    $stmt->bindParam(':me',      $userId, PDO::PARAM_INT);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    sendResponse(200, [
        'post_id'     => $postId,
        'like_count'  => (int)$result['like_count'],
        'liked_by_me' => (bool)$result['liked_by_me']
    ]);
}

// ── POST /api/like.php?post_id=X — toggle like/unlike ───────────
elseif ($method === 'POST') {

    $currentUser = requireAuth();
    $userId      = $currentUser['user_id'];
    $postId      = isset($_GET['post_id']) && is_numeric($_GET['post_id']) ? (int)$_GET['post_id'] : null;

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

    // Check if already liked
    $existing = $pdo->prepare("SELECT id FROM likes WHERE user_id = :user_id AND post_id = :post_id LIMIT 1");
    $existing->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $existing->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $existing->execute();

    if ($existing->fetch()) {
        // Already liked — unlike
        $del = $pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id");
        $del->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $del->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $del->execute();

        $action = 'unliked';
    } else {
        // Not liked — like
        $ins = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)");
        $ins->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $ins->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $ins->execute();

        $action = 'liked';
    }

    // Return updated count
    $count = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = :post_id");
    $count->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $count->execute();

    sendResponse(200, [
        'action'      => $action,
        'post_id'     => $postId,
        'like_count'  => (int)$count->fetchColumn(),
        'liked_by_me' => $action === 'liked'
    ]);
}

else {
    sendResponse(405, ['error' => 'Method not allowed']);
}
?>