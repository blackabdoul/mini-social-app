<?php
session_start();
if(!isset($_SESSION["id_user"])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(90deg, rgba(131, 58, 180, 1) 0%, rgba(253, 29, 29, 1) 50%, rgba(252, 176, 69, 1) 100%);
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
            color: white;
        }
        .welcome-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        h1 {
            margin-bottom: 30px;
        }
        .nav-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 25px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="welcome-box">
        <h1>Welcome, <?= htmlspecialchars($_SESSION["user_email"]) ?></h1>
        <p>You have successfully logged in!</p>
        
        <div class="nav-links">
            <a href="profile.php">👤 My Profile</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>
</body>
</html>
