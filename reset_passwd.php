<?php
session_start();
require_once "config.php";

$token = $_GET['token'] ?? '';
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error']);
unset($_SESSION['success']);

$validToken = false;
$email = '';

// Validate token
if ($token) {
    $stmt = $pdo->prepare(
        "SELECT id, email, reset_token_expires_at 
         FROM users 
         WHERE reset_token = :token 
         LIMIT 1"
    );
    $stmt->bindParam(":token", $token, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && strtotime($user['reset_token_expires_at']) > time()) {
        $validToken = true;
        $email = $user['email'];
    } else {
        $error = "This password reset link is invalid or has expired.";
    }
}

// Handle password reset
if ($_SERVER["REQUEST_METHOD"] === "POST" && $validToken) {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (!$newPassword || !$confirmPassword) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: reset_passwd.php?token=" . $token);
        exit();
    }
    
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: reset_passwd.php?token=" . $token);
        exit();
    }
    
    if (strlen($newPassword) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters.";
        header("Location: reset_passwd.php?token=" . $token);
        exit();
    }

    $simpassStmt = $pdo->prepare(
        "SELECT id, password 
        FROM users 
        WHERE reset_token = :token 
        LIMIT 1"
    );

    $simpassStmt->bindParam(":token", $token, PDO::PARAM_STR);
    $simpassStmt->execute();
    $user = $simpassStmt->fetch(PDO::FETCH_ASSOC);

    if(password_verify($newPassword, $user['password'])){
        $_SESSION['error']= "New password must be different from the previous one";
        header("Location: reset_passwd.php?token=" . $token);
        exit(); 

    } else {
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $updateStmt = $pdo->prepare(
            "UPDATE users 
            SET password = :password, 
                reset_token = NULL, 
                reset_token_expires_at = NULL,
                updated_at = NOW()
            WHERE reset_token = :token"
        );
        $updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $updateStmt->bindParam(':token', $token, PDO::PARAM_STR);
        
        if ($updateStmt->execute()) {
            $_SESSION['success'] = "Password reset successfully! You can now login.";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to reset password. Please try again.";
            header("Location: reset_passwd.php?token=" . $token);
            exit();
        }

    }
    
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: radial-gradient(circle, rgba(63, 94, 251, 1) 0%, rgba(252, 70, 107, 1) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        .box {
            background: #ffffff;
            padding: 30px;
            width: 400px;
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
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
        .info {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Reset Password</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($validToken): ?>
            <div class="info">
                Resetting password for: <strong><?= htmlspecialchars($email) ?></strong>
            </div>
            
            <form method="POST">
                <label>New Password</label>
                <input
                    type="password"
                    name="new_password"
                    placeholder="Enter new password"
                    required
                    autofocus
                >
                
                <label>Confirm New Password</label>
                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Confirm new password"
                    required
                >
                
                <button type="submit">Reset Password</button>
            </form>
        <?php else: ?>
            <p style="text-align: center; color: #666;">
                Invalid or expired reset link. Please request a new one.
            </p>
        <?php endif; ?>
        
        <div class="link">
            <a href="index.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>