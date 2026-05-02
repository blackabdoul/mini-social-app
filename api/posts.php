<?php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

// Optional pagination
$limit  = isset($_GET['limit'])  && is_numeric($_GET['limit'])  ? (int)$_GET['limit']  : 20;
$offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Cap limit to prevent abuse
if ($limit > 100) $limit = 100;

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
    ORDER BY p.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindParam(':limit',  $limit,  PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination metadata
$countStmt = $pdo->query("SELECT COUNT(*) FROM posts");
$total = (int)$countStmt->fetchColumn();

sendResponse(200, [
    'total'  => $total,
    'limit'  => $limit,
    'offset' => $offset,
    'posts'  => $posts
]);
?>