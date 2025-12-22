<?php

session_start();
$error = $_SESSION['error'] ?? ''; // Read error from session
unset($_SESSION['error']); // Clear it immediately
$success = $_SESSION['success'] ?? ''; // Read error from session
unset($_SESSION['success']); // Clear it immediately

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #3F5EFB;
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
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
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
    
    <p style="text-align:center; margin-top:10px;">
        Don’t have an account?
        <a href="register.php">Sign up</a>
    </p>

</div>

</body>
</html>
