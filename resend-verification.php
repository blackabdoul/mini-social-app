<?php
session_start();
require_once "config.php";
require_once "email_config.php";

// Check if this is from the button (has email in POST)
$from_button = isset($_POST['email']) && !empty($_POST['email']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header("Location: resend-verification.php");
        exit();
    }
    
    // Check if user exists and is not verified
    $stmt = $pdo->prepare("SELECT id, is_verified FROM users WHERE email = :email LIMIT 1");
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['error'] = "No account found with this email.";
        header("Location: resend-verification.php");
        exit();
    }
    
    if ($user['is_verified'] == 1) {
        $_SESSION['error'] = "This email is already verified. You can login.";
        header("Location: index.php");
        exit();
    }
    
    // Generate new verification token
    $verificationToken = bin2hex(random_bytes(32));
    $tokenExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // Update token in database
    $updateStmt = $pdo->prepare(
        "UPDATE users 
         SET verification_token = :token, 
             token_expires_at = :expires 
         WHERE id = :id"
    );
    $updateStmt->bindParam(":token", $verificationToken, PDO::PARAM_STR);
    $updateStmt->bindParam(":expires", $tokenExpires, PDO::PARAM_STR);
    $updateStmt->bindParam(":id", $user['id'], PDO::PARAM_INT);
    $updateStmt->execute();
    
    // Send verification email
    $verificationLink = "http://localhost/myApp/verify.php?token=" . $verificationToken;
    
    $to = $email;
    $subject = "Verify Your Email Address";
    $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #667eea;'>Verify Your Email 📧</h2>
                <p>You requested a new verification email. Please click the button below to verify your email address:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$verificationLink' style='background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Verify Email Address</a>
                </div>
                <p>Or copy this link: <br><strong>$verificationLink</strong></p>
                <p style='color: #666; font-size: 12px;'>This link expires in 24 hours.</p>
            </div>
        </body>
        </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@yoursite.com" . "\r\n";
    
    if (sendEmail($to, $subject, $message)) {
        // ✅ Success - ALWAYS redirect to login (never show form)
        $_SESSION['success'] = "Verification email sent! Please check your inbox.";
        header("Location: index.php");
        exit();
    } else {
        // ❌ Failed - redirect to login with error
        $_SESSION['error'] = "Failed to send email. Please try again later.";
        header("Location: index.php");
        exit();
    }
}

// If we reach here, it's a GET request (link clicked, not button)
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error']);
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification Email</title>
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
        <h2>Resend Verification Email</h2>
        <p>Enter your email address and we'll send you a new verification link.</p>
        
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
            >
            <button type="submit">Send Verification Email</button>
        </form>
        
        <div class="link">
            <a href="index.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
