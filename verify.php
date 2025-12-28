<?php
session_start();
require_once "config.php";

$message = "";
$messageType = "error";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Find user with this token
    $stmt = $pdo->prepare(
        "SELECT id, email, token_expires_at, is_verified 
         FROM users 
         WHERE verification_token = :token 
         LIMIT 1"
    );
    $stmt->bindParam(":token", $token, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // Token doesn't exist in database
        $message = "Invalid verification link.";
        $messageType = "error";
    } elseif ($user['is_verified'] == 1) {
        // Already verified
        $message = "This email has already been verified. You can login now.";
        $messageType = "info";
    } elseif (strtotime($user['token_expires_at']) < time()) {
        // Token expired
        $message = "This verification link has expired. Please request a new one.";
        $messageType = "error";
    } else {
        // Everything is valid - verify the user!
        $updateStmt = $pdo->prepare(
            "UPDATE users 
             SET is_verified = 1, 
                 verification_token = NULL, 
                 token_expires_at = NULL 
             WHERE id = :id"
        );
        $updateStmt->bindParam(":id", $user['id'], PDO::PARAM_INT);
        
        if ($updateStmt->execute()) {
            $message = "Email verified successfully! You can now login.";
            $messageType = "success";
            $_SESSION['success'] = "Email verified! Please login.";
        } else {
            $message = "Verification failed. Please try again.";
            $messageType = "error";
        }
    }
} else {
    // No token provided in URL
    $message = "No verification token provided.";
    $messageType = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .verification-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .icon.success {
            background: #d4edda;
            color: #28a745;
        }

        .icon.error {
            background: #f8d7da;
            color: #dc3545;
        }

        .icon.info {
            background: #d1ecf1;
            color: #0c5460;
        }

        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
        }

        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #5568d3;
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class="verification-box">
        <!-- Icon based on message type -->
        <div class="icon <?= $messageType ?>">
            <?php if ($messageType === 'success'): ?>
                ✓
            <?php elseif ($messageType === 'info'): ?>
                ℹ
            <?php else: ?>
                ✗
            <?php endif; ?>
        </div>

        <h1>Email Verification</h1>
        
        <!-- The message -->
        <div class="message <?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>

        <!-- Button to login -->
        <a href="index.php" class="btn">Go to Login</a>
    </div>
</body>
</html>
