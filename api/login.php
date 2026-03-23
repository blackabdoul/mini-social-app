<?php
require_once "config.php";
require_once "auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

$data = getRequestBody();

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    sendResponse(400, ['error' => 'Email and password required']);
}

// Check user
$stmt = $pdo->prepare("SELECT id, email, password, role, is_verified FROM users WHERE email = :email LIMIT 1");
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    sendResponse(401, ['error' => 'Invalid credentials']);
}

if ($user['is_verified'] == 0) {
    sendResponse(403, ['error' => 'Email not verified']);
}

// Generate token
$token = generateJWT($user['id'], $user['email'], $user['role']);

sendResponse(200, [
    'message' => 'Login successful',
    'token' => $token,
    'user' => [
        'id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role']
    ]
]);
?>