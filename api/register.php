<?php
require_once "config.php";
require_once __DIR__ . "/../email_config.php";

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
    $newUserId = $pdo->lastInsertId();
 
    // Send verification email (mirrors what register.php does in the session layer)
    $verificationLink = ($_ENV['APP_URL'] ?? 'http://localhost/myApp') . '/verify.php?token=' . $verificationToken;
 
    $subject = 'Verify Your Email Address';
    $body    = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #667eea;'>Verify Your Email</h2>
                    <p>Thanks for registering! Please click the button below to verify your email address:</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='$verificationLink'
                        style='background: #667eea; color: white; padding: 15px 30px;
                                text-decoration: none; border-radius: 5px; display: inline-block;'>
                            Verify Email Address
                        </a>
                    </div>
                    <p>Or copy this link:<br><strong>$verificationLink</strong></p>
                    <p style='color: #666; font-size: 12px;'>This link expires in 24 hours.</p>
                </div>
            </body>
            </html>
            ";
 
    sendEmail($email, $subject, $body);
 
    sendResponse(201, [
        'message'               => 'User registered successfully. Please check your email to verify your account',
        'user_id'               => $newUserId,
        'verification_required' => true
    ]);
} else {
    sendResponse(500, ['error' => 'Registration failed']);
}
?>