<?php
session_start();
require_once "config.php";

// Handle login submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email && $password) {
        $sql = "SELECT id, email, is_verified, password FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 🔐 Authentication happens HERE
        if ($user && password_verify($password, $user["password"])) {

            if ($user['is_verified'] == 0) {
                // ✨ NEW: Store email in session for resend page
                $_SESSION['unverified_email'] = $email;
                $_SESSION['error'] = "Please verify your email before logging in.";
                header("Location: index.php");
                exit();
            }

            $_SESSION["id_user"] = $user["id"];
            $_SESSION["user_email"] = $user["email"];

            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: index.php");
            exit();
        }

        
    } else {
        $_SESSION['error'] = "All fields are required.";
        header("Location: index.php");
        exit();
    }
}

?>