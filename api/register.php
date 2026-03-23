<?php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

$data = getRequestBody();

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    sendResponse(400, ['error' => 'Email and password required']);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(400, ['error' => 'Invalid email format']);
}

if (strlen($password) < 6) {
    sendResponse(400, ['error' => 'Password must be at least 6 characters']);
}

// Check if email exists
$check = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$check->bindParam(':email', $email, PDO::PARAM_STR);
$check->execute();

if ($check->fetch()) {
    sendResponse(409, ['error' => 'Email already registered']);
}

// Create user
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$verificationToken = bin2hex(random_bytes(32));
$tokenExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

$stmt = $pdo->prepare(
    "INSERT INTO users (email, password, verification_token, token_expires_at, is_verified, role) 
     VALUES (:email, :password, :token, :expires, 0, 'user')"
);
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
$stmt->bindParam(':token', $verificationToken, PDO::PARAM_STR);
$stmt->bindParam(':expires', $tokenExpires, PDO::PARAM_STR);

if ($stmt->execute()) {
    sendResponse(201, [
        'message' => 'User registered successfully',
        'user_id' => $pdo->lastInsertId(),
        'verification_required' => true
    ]);
} else {
    sendResponse(500, ['error' => 'Registration failed']);
}
?>