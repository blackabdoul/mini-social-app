<?php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

$data = getRequestBody();
$token = trim($data['token'] ?? '');

if (!$token) {
    sendResponse(400, ['error' => 'Verification token is required']);
}

// Look up the token
$stmt = $pdo->prepare(
    "SELECT id, email, is_verified, token_expires_at 
     FROM users 
     WHERE verification_token = :token 
     LIMIT 1"
);
$stmt->bindParam(':token', $token, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    sendResponse(400, ['error' => 'Invalid verification token']);
}

if ($user['is_verified'] == 1) {
    sendResponse(409, ['error' => 'Email is already verified']);
}

if (strtotime($user['token_expires_at']) < time()) {
    sendResponse(410, ['error' => 'Verification token has expired', 'hint' => 'Request a new one via POST /api/resend-verification']);
}

// Activate the account
$update = $pdo->prepare(
    "UPDATE users 
     SET is_verified = 1, 
         verification_token = NULL, 
         token_expires_at = NULL 
     WHERE id = :id"
);
$update->bindParam(':id', $user['id'], PDO::PARAM_INT);

if ($update->execute()) {
    sendResponse(200, [
        'message' => 'Email verified successfully',
        'email'   => $user['email']
    ]);
} else {
    sendResponse(500, ['error' => 'Verification failed, please try again']);
}
?>