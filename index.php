<?php
session_start();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
$unverified_email = $_SESSION['unverified_email'] ?? '';

if(isset($_GET['account_deleted'])){
    $success="Account successfully deleted";
}

unset($_SESSION['error']);
unset($_SESSION['success']);
unset($_SESSION['unverified_email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
        .login-box {
            background: #ffffff;
            padding: 30px;
            width: 330px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-box label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .login-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        .login-box button {
            width: 100%;
            padding: 10px;
            background: #2563eb;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            box-sizing: border-box;
        }
        .login-box button:hover {
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
            font-size: 14px;
        }
        .success {
            color: #16a34a;
            background: #dcfce7;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #86efac;
            font-size: 14px;
        }
        .links {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .links a {
            color: #2563eb;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .divider {
            text-align: center;
            margin: 10px 0;
            color: #999;
        }
        .action-button {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .action-button:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        
        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
                
                <?php if ($unverified_email): ?>
                    <!-- ✨ NEW: Show resend button for unverified users -->
                    <form method="POST" action="resend-verification.php" style="margin-top: 10px;">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($unverified_email) ?>">
                        <button type="submit" class="action-button" style="border: none; cursor: pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg> Resend Verification Email
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="indexback.php">
            <label>Email</label>
            <input type="email" name="email" required>
            
            <label>Password</label>
            <input type="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>        
        
        <div class="links">
            Don't have an account?
            <a href="register.php">Sign up</a>
        </div>

        <div class="divider">•</div>

        <div class="links">
            <a href="forgot_passwd.php">Forgot password?</a>
        </div>
        
       <?php if (!$unverified_email): ?>
            <div class="divider">•</div>
            
            <div class="links">
                <a href="resend-verification.php">Resend verification email</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>