<?php
require_once "config.php";
require_once "auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

$currentUser = requireAuth();

// Extract the raw token from the Authorization header
$headers     = getallheaders();
$authHeader  = $headers['Authorization'] ?? $headers['authorization'] ?? '';
$token       = str_replace('Bearer ', '', $authHeader);

// Blacklist the token until its natural expiry
// We store it so requireAuth() can reject it on future requests
$expiresAt = date('Y-m-d H:i:s', $currentUser['exp']);

$stmt = $pdo->prepare(
    "INSERT IGNORE INTO token_blacklist (token, expires_at) 
     VALUES (:token, :expires_at)"
);
$stmt->bindParam(':token',      $token,     PDO::PARAM_STR);
$stmt->bindParam(':expires_at', $expiresAt, PDO::PARAM_STR);

if ($stmt->execute()) {
    sendResponse(200, ['message' => 'Logged out successfully']);
} else {
    sendResponse(500, ['error' => 'Logout failed']);
}
?>