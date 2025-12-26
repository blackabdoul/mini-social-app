<?php
session_start();
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: register.php");
        exit();
    } elseif ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php");
        exit();
    } elseif (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters.";
        header("Location: register.php");
        exit();
    } else {
        // Check if email already exists
        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email = :email LIMIT 1"
        );
        $check->bindParam(":email", $email, PDO::PARAM_STR);
        $check->execute();

        if ($check->fetch()) {
            $_SESSION['error'] = "Account already exists. Please login.";
            header("Location: index.php");
            exit();
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (email, password) 
                 VALUES (:email, :password)"
            );
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);

            if ($stmt->execute()) {
                
                $_SESSION['success'] = "Registration successful! Please log in";
                header("Location: index.php");
                exit();
                 
            } else {
                $_SESSION['error'] = "Registration failed. Try again.";
                header("Location: register.php");
                exit();
            }
        }
    } 
} else {
    // If accessed directly without POST, redirect back
    header("Location: register.php");
    exit();
}
?>