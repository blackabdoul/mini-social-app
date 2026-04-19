<?php
require_once "config.php";
require_once "auth.php";

// Get user ID from URL (e.g., /api/user.php?id=5)
$userId = $_GET['id'] ?? null;

if (!$userId || !is_numeric($userId)) {
    sendResponse(400, ['error' => 'Valid user ID required']);
}

// GET - Retrieve user
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $currentUser = requireAuth();
    
    // Users can only view themselves unless admin
    if ($currentUser['user_id'] != $userId && $currentUser['role'] !== 'admin') {
        sendResponse(403, ['error' => 'Access denied']);
    }
    
    $stmt = $pdo->prepare("
        SELECT id, email, full_name, phone, bio, location, dob, role, is_verified, created_at, updated_at 
        FROM users 
        WHERE id = :id
    ");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        sendResponse(404, ['error' => 'User not found']);
    }
    
    sendResponse(200, ['user' => $user]);
}

// PUT - Update user
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $currentUser = requireAuth();
    
    // Users can only update themselves unless admin
    if ($currentUser['user_id'] != $userId && $currentUser['role'] !== 'admin') {
        sendResponse(403, ['error' => 'Access denied']);
    }
    
    $data = getRequestBody();
    
    $fullName = trim($data['full_name'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $bio = trim($data['bio'] ?? '');
    $location = trim($data['location'] ?? '');
    $dob      = !empty($data['dob']) ? $data['dob'] : null;
    
    $stmt = $pdo->prepare("
        UPDATE users 
        SET full_name = :full_name, 
            phone = :phone, 
            bio = :bio, 
            location = :location,
            updated_at = NOW()
        WHERE id = :id
    ");
    $stmt->bindParam(':full_name', $fullName, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
    $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        sendResponse(200, ['message' => 'User updated successfully']);
    } else {
        sendResponse(500, ['error' => 'Update failed']);
    }
}

// DELETE - Delete user (admin can delete anyone; users can delete only themselves)
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $currentUser = requireAuth();
 
    $isSelf  = $currentUser['user_id'] == $userId;
    $isAdmin = $currentUser['role'] === 'admin';
 
    if (!$isSelf && !$isAdmin) {
        sendResponse(403, ['error' => 'Access denied']);
    }
 
    // Admins cannot delete themselves via this endpoint
    if ($isAdmin && $isSelf) {
        sendResponse(403, ['error' => 'Admins cannot delete their own account']);
    }
 
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
 
    if ($stmt->execute()) {
        sendResponse(200, ['message' => 'User deleted successfully']);
    } else {
        sendResponse(500, ['error' => 'Delete failed']);
    }
}

else {
    sendResponse(405, ['error' => 'Method not allowed']);
}
?>