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

// ── CREATE COMMENT ───────────────────────────────────────────────
if ($action === 'create_comment') {

    $postId  = (int)($_POST['post_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');

    if (!$postId) {
        $_SESSION['error'] = "Invalid post.";
        header("Location: dashboard.php");
        exit();
    }

    if (!$content) {
        $_SESSION['error'] = "Comment cannot be empty.";
        header("Location: dashboard.php#post-{$postId}");
        exit();
    }

    if (strlen($content) > 500) {
        $_SESSION['error'] = "Comment exceeds 500 characters.";
        header("Location: dashboard.php#post-{$postId}");
        exit();
    }

    // Verify post exists
    $check = $pdo->prepare("SELECT id FROM posts WHERE id = :id LIMIT 1");
    $check->bindParam(':id', $postId, PDO::PARAM_INT);
    $check->execute();
    if (!$check->fetch()) {
        $_SESSION['error'] = "Post not found.";
        header("Location: dashboard.php");
        exit();
    }

    $stmt = $pdo->prepare(
        "INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)"
    );
    $stmt->bindParam(':post_id', $postId,  PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId,  PDO::PARAM_INT);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['open_comments'] = $postId; // tell dashboard to auto-open this post's comments
    } else {
        $_SESSION['error'] = "Failed to post comment.";
    }

    header("Location: dashboard.php");
    exit();
}

// ── DELETE COMMENT ───────────────────────────────────────────────
if ($action === 'delete_comment') {

    $commentId = (int)($_POST['comment_id'] ?? 0);

    if (!$commentId) {
        $_SESSION['error'] = "Invalid comment.";
        header("Location: dashboard.php");
        exit();
    }

    // Fetch comment to check ownership
    $stmt = $pdo->prepare("SELECT user_id, post_id FROM comments WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $commentId, PDO::PARAM_INT);
    $stmt->execute();
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment) {
        $_SESSION['error'] = "Comment not found.";
        header("Location: dashboard.php");
        exit();
    }

    $isSelf  = $comment['user_id'] == $userId;
    $isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';

    if (!$isSelf && !$isAdmin) {
        $_SESSION['error'] = "You don't have permission to delete this comment.";
        header("Location: dashboard.php");
        exit();
    }

    $del = $pdo->prepare("DELETE FROM comments WHERE id = :id");
    $del->bindParam(':id', $commentId, PDO::PARAM_INT);

    if ($del->execute()) {
        $_SESSION['open_comments'] = $comment['post_id'];
    } else {
        $_SESSION['error'] = "Failed to delete comment.";
    }

    header("Location: dashboard.php");
    exit();
}

header("Location: dashboard.php");
exit();