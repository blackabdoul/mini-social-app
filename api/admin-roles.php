<?php
require_once "config.php";
require_once "auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

// Admin only
$admin = requireAdmin();

$data   = getRequestBody();
$userId = isset($data['user_id']) ? (int) $data['user_id'] : null;

if (!$userId) {
    sendResponse(400, ['error' => 'user_id is required']);
}

// Admins cannot change their own role
if ($userId === (int) $admin['user_id']) {
    sendResponse(403, ['error' => 'You cannot change your own role']);
}

// Fetch target user's current role
$stmt = $pdo->prepare("SELECT id, email, role FROM users WHERE id = :id LIMIT 1");
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$target = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$target) {
    sendResponse(404, ['error' => 'User not found']);
}

// Toggle the role
$newRole = $target['role'] === 'admin' ? 'user' : 'admin';

$update = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
$update->bindParam(':role', $newRole,  PDO::PARAM_STR);
$update->bindParam(':id',   $userId,   PDO::PARAM_INT);

if ($update->execute()) {
    sendResponse(200, [
        'message'      => 'Role updated successfully',
        'user_id'      => $userId,
        'email'        => $target['email'],
        'previous_role'=> $target['role'],
        'new_role'     => $newRole
    ]);
} else {
    sendResponse(500, ['error' => 'Failed to update role']);
}
?>