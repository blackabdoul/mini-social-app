<?php
session_start();
require_once "config.php";
require_once "email_config.php";

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error']);
unset($_SESSION['success']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header("Location: forgot_passwd.php");
        exit();
    }
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = :email LIMIT 1");
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));
        $tokenExpires = date('Y-m-d H:i:s', strtotime('+1 hour')); // 1 hour validity
        
        // Save token to database
        $updateStmt = $pdo->prepare(
            "UPDATE users 
             SET reset_token = :token, 
                 reset_token_expires_at = :expires 
             WHERE id = :id"
        );
        $updateStmt->bindParam(':token', $resetToken, PDO::PARAM_STR);
        $updateStmt->bindParam(':expires', $tokenExpires, PDO::PARAM_STR);
        $updateStmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
        $updateStmt->execute();
        
        // Send reset email
        $resetLink = "http://localhost/myApp/reset_passwd.php?token=" . $resetToken;
        
        $to = $email;
        $subject = "Password Reset Request";
        $message = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #667eea;'>Password Reset Request 🔐</h2>
                    <p>You requested to reset your password. Click the button below to create a new password:</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='$resetLink' style='background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
                    </div>
                    <p>Or copy this link: <br><strong>$resetLink</strong></p>
                    <p style='color: #dc3545; font-weight: bold;'>This link expires in 1 hour.</p>
                    <p style='color: #666; font-size: 12px;'>If you didn't request this, you can safely ignore this email.</p>
                </div>
            </body>
            </html>
        ";
        
        sendEmail($to, $subject, $message);
    }
    
    // Always show success (security: don't reveal if email exists)
    $_SESSION['success'] = "If that email exists, we've sent password reset instructions. Check your mailbox";
    header("Location: forgot_passwd.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: radial-gradient(circle, rgba(63, 94, 251, 1) 0%, rgba(252, 70, 107, 1) 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .box {
            background: #ffffff;
            padding: 30px;
            width: 400px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
        }
        p {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            box-sizing: border-box;
        }
        button:hover {
            background: #1e40af;
        }
        .error {
            color: #dc2626;
            background: #fee2e2;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #fca5a5;
        }
        .success {
            color: #16a34a;
            background: #dcfce7;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #86efac;
        }
        .link {
            text-align: center;
            margin-top: 15px;
        }
        .link a {
            color: #2563eb;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>🔐 Forgot Password</h2>
        <p>Enter your email address and we'll send you instructions to reset your password.</p>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required
                autofocus
            >
            <button type="submit">Send Reset Link</button>
        </form>
        
        <div class="link">
            <a href="index.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>