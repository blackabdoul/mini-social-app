<?php
require_once "config.php";
require_once "auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

// Require admin
requireAdmin();

// Get all users
$stmt = $pdo->query("
    SELECT id, email, full_name, role, is_verified, created_at 
    FROM users 
    ORDER BY created_at DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendResponse(200, [
    'count' => count($users),
    'users' => $users
]);
?>