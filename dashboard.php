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
        .b{
            background: #833AB4;
            background: linear-gradient(90deg, rgba(131, 58, 180, 1) 0%, rgba(253, 29, 29, 1) 50%, rgba(252, 176, 69, 1) 100%);
        }
        body {
            background: linear-gradient(90deg, rgba(131, 58, 180, 1) 0%, rgba(253, 29, 29, 1) 50%, rgba(252, 176, 69, 1) 100%);
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
            color: white;
        }
        a {
            color: white;
            text-decoration: none;
            background: rgba(0,0,0,0.3);
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        a:hover {
            background: rgba(0,0,0,0.5);
        }
    </style>
</head>
<body class= b>
    <h1>Welcome, <?= htmlspecialchars($_SESSION["user_email"]) ?></h1>
    <a href="logout.php">Logout</a>
</body>
</html>
