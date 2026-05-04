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

$userId = $_SESSION['id_user'];
$postId = (int)($_POST['post_id'] ?? 0);

if (!$postId) {
    $_SESSION['error'] = "Invalid post.";
    header("Location: dashboard.php");
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

// Check if like already exists
$existing = $pdo->prepare("SELECT id FROM likes WHERE user_id = :user_id AND post_id = :post_id LIMIT 1");
$existing->bindParam(':user_id', $userId, PDO::PARAM_INT);
$existing->bindParam(':post_id', $postId, PDO::PARAM_INT);
$existing->execute();

if ($existing->fetch()) {
    // Already liked — unlike it
    $del = $pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id");
    $del->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $del->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $del->execute();
} else {
    // Not liked yet — like it
    $ins = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)");
    $ins->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $ins->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $ins->execute();
}

header("Location: dashboard.php");
exit();