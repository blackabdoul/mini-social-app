<?php
require_once "config.php";
require_once __DIR__ . "/../email_config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ['error' => 'Method not allowed']);
}

$data  = getRequestBody();
$email = trim($data['email'] ?? '');

if (!$email) {
    sendResponse(400, ['error' => 'Email is required']);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(400, ['error' => 'Invalid email format']);
}

// Generic response used in all cases — never reveal whether the email exists
$genericSuccess = ['message' => 'If that email is registered, a password reset link has been sent'];

// Look up user silently
$stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = :email LIMIT 1");
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    sendResponse(200, $genericSuccess); // Don't leak account existence
}

// Generate reset token (1-hour expiry)
$resetToken  = bin2hex(random_bytes(32));
$tokenExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

$update = $pdo->prepare(
    "UPDATE users 
     SET reset_token            = :token, 
         reset_token_expires_at = :expires 
     WHERE id = :id"
);
$update->bindParam(':token',   $resetToken,  PDO::PARAM_STR);
$update->bindParam(':expires', $tokenExpiry, PDO::PARAM_STR);
$update->bindParam(':id',      $user['id'],  PDO::PARAM_INT);
$update->execute();

// Build and send the email
$resetLink = ($_ENV['APP_URL'] ?? 'http://localhost/myApp') . '/reset_passwd.php?token=' . $resetToken;

$subject = 'Password Reset Request';
$body    = "
<html>
<body style='font-family: Arial, sans-serif;'>
    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
        <h2 style='color: #667eea;'>Password Reset Request</h2>
        <p>You requested to reset your password. Click the button below to set a new one:</p>
        <div style='text-align: center; margin: 30px 0;'>
            <a href='$resetLink'
               style='background: #667eea; color: white; padding: 15px 30px;
                      text-decoration: none; border-radius: 5px; display: inline-block;'>
                Reset Password
            </a>
        </div>
        <p>Or copy this link:<br><strong>$resetLink</strong></p>
        <p style='color: #dc3545; font-weight: bold;'>This link expires in 1 hour.</p>
        <p style='color: #666; font-size: 12px;'>If you didn't request this, you can safely ignore this email.</p>
    </div>
</body>
</html>
";

sendEmail($email, $subject, $body); // Fire-and-forget — don't leak success/failure

sendResponse(200, $genericSuccess);
?>
