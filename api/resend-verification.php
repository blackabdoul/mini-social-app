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

// Look up the user
$stmt = $pdo->prepare("SELECT id, is_verified FROM users WHERE email = :email LIMIT 1");
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Always return 200 — never reveal whether an email is registered (security)
if (!$user) {
    sendResponse(200, ['message' => 'If that email exists and is unverified, a new link has been sent']);
}

if ($user['is_verified'] == 1) {
    sendResponse(409, ['error' => 'This email is already verified, you can log in']);
}

// Generate fresh token
$newToken   = bin2hex(random_bytes(32));
$newExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

$update = $pdo->prepare(
    "UPDATE users 
     SET verification_token = :token, 
         token_expires_at   = :expires 
     WHERE id = :id"
);
$update->bindParam(':token',   $newToken,   PDO::PARAM_STR);
$update->bindParam(':expires', $newExpires, PDO::PARAM_STR);
$update->bindParam(':id',      $user['id'], PDO::PARAM_INT);
$update->execute();

// Build and send the email
$verificationLink = ($_ENV['APP_URL'] ?? 'http://localhost/myApp') . '/verify.php?token=' . $newToken;

$subject = 'Verify Your Email Address';
$body    = "
<html>
<body style='font-family: Arial, sans-serif;'>
    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
        <h2 style='color: #667eea;'>Verify Your Email</h2>
        <p>You requested a new verification email. Click the button below to verify your address:</p>
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

if (sendEmail($email, $subject, $body)) {
    sendResponse(200, ['message' => 'Verification email sent, please check your inbox']);
} else {
    sendResponse(500, ['error' => 'Failed to send email, please try again later']);
}
?>