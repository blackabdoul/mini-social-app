<?php
require_once "config.php";
require_once "auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

// Require authentication
$currentUser = requireAuth();

// Get request data
$data = getRequestBody();

$currentPassword = $data['current_password'] ?? '';
$newPassword = $data['new_password'] ?? '';
$confirmPassword = $data['confirm_password'] ?? '';

// Validate all fields present
if (!$currentPassword || !$newPassword || !$confirmPassword) {
    sendResponse(400, ['error' => 'All password fields are required']);
}

// Validate passwords match
if ($newPassword !== $confirmPassword) {
    sendResponse(400, ['error' => 'New passwords do not match']);
}

// Validate password length
if (strlen($newPassword) < 6) {
    sendResponse(400, ['error' => 'Password must be at least 6 characters']);
}

// Get user's current password from database
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
$stmt->bindParam(':id', $currentUser['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    sendResponse(404, ['error' => 'User not found']);
}

// Verify current password
if (!password_verify($currentPassword, $user['password'])) {
    sendResponse(401, ['error' => 'Current password is incorrect']);
}

// Hash new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update password in database
$updateStmt = $pdo->prepare("
    UPDATE users 
    SET password = :password, 
        updated_at = NOW() 
    WHERE id = :id
");
$updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
$updateStmt->bindParam(':id', $currentUser['user_id'], PDO::PARAM_INT);

if ($updateStmt->execute()) {
    sendResponse(200, ['message' => 'Password changed successfully']);
} else {
    sendResponse(500, ['error' => 'Failed to change password']);
}
?>