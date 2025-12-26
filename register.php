<?php 
session_start();
$error= $_SESSION['error'];
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #020024;
            background: linear-gradient(90deg, rgba(2, 0, 36, 1) 0%, rgba(9, 9, 121, 1) 35%, rgba(0, 212, 255, 1) 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .box {
            background: #fff;
            padding: 30px;
            width: 350px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            box-sizing: border-box;  
        }
        button {
            background: #2563eb;
            color: #fff;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #1e40af;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        .link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Create Account</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="registerback.php">
        <input
            name="email"
            placeholder="Email"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >

        <input
            type="password"
            name="confirm_password"
            placeholder="Confirm Password"
            required
        >

        <button type="submit">Sign Up</button>
    </form>

    <div class="link">
        <a href="index.php">Back to login</a>
    </div>
</div>

</body>
</html>
