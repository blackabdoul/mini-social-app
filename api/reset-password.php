<?php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

$data            = getRequestBody();
$token           = trim($data['token']            ?? '');
$newPassword     = $data['new_password']           ?? '';
$confirmPassword = $data['confirm_password']       ?? '';

// Validate fields present
if (!$token || !$newPassword || !$confirmPassword) {
    sendResponse(400, ['error' => 'Token, new_password, and confirm_password are all required']);
}

// Validate password rules
if ($newPassword !== $confirmPassword) {
    sendResponse(400, ['error' => 'Passwords do not match']);
}

if (strlen($newPassword) < 6) {
    sendResponse(400, ['error' => 'Password must be at least 6 characters']);
}

// Validate the token
$stmt = $pdo->prepare(
    "SELECT id, password, reset_token_expires_at 
     FROM users 
     WHERE reset_token = :token 
     LIMIT 1"
);
$stmt->bindParam(':token', $token, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    sendResponse(400, ['error' => 'Invalid reset token']);
}

if (strtotime($user['reset_token_expires_at']) < time()) {
    sendResponse(410, ['error' => 'Reset token has expired', 'hint' => 'Request a new link via POST /api/forgot-password']);
}

// Prevent reuse of the old password
if (password_verify($newPassword, $user['password'])) {
    sendResponse(400, ['error' => 'New password must be different from your current password']);
}

// Update password and clear the token
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$update = $pdo->prepare(
    "UPDATE users 
     SET password               = :password, 
         reset_token            = NULL, 
         reset_token_expires_at = NULL,
         updated_at             = NOW()
     WHERE id = :id"
);
$update->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
$update->bindParam(':id',       $user['id'],     PDO::PARAM_INT);

if ($update->execute()) {
    sendResponse(200, ['message' => 'Password reset successfully, you can now log in']);
} else {
    sendResponse(500, ['error' => 'Failed to reset password, please try again']);
}
?>