<?php
session_start();
require_once "config.php";
require_once "email_config.php";

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
            header("Location: register.php");
            exit();
        } else {
            // ✨ NEW: Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            $tokenExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // ✨ NEW: Insert with verification fields
            $stmt = $pdo->prepare(
                "INSERT INTO users (email, password, verification_token, token_expires_at, is_verified) 
                 VALUES (:email, :password, :token, :expires, 0)"
            );
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(":token", $verificationToken, PDO::PARAM_STR);
            $stmt->bindParam(":expires", $tokenExpires, PDO::PARAM_STR);

            if ($stmt->execute()) {

                $verificationLink = "http://localhost/myApp/verify.php?token=" . $verificationToken;
                
                $to = $email;
                $subject = "Verify Your Email Address";
                $message = "
                    <html>
                    <body style='font-family: Arial, sans-serif;'>
                        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                            <h2 style='color: #667eea;'>Welcome! 🎉</h2>
                            <p>Thank you for registering! Please verify your email address by clicking the button below:</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='$verificationLink' style='background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Verify Email Address</a>
                            </div>
                            <p>Or copy this link: <br><strong>$verificationLink</strong></p>
                            <p style='color: #666; font-size: 12px;'>This link expires in 1 hour.</p>
                        </div>
                    </body>
                    </html>
                ";
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: noreply@yoursite.com" . "\r\n";

                // Try to send email
                if(sendEmail($to, $subject, $message)){
                    $_SESSION['success'] = "Registration successful! Please check your email to verify your account.";
                    header("Location: index.php");
                    exit();
                }else {
                    $_SESSION['error'] = "Failed to send email. Please try again later.";
                    header("Location: index.php");
                    exit();
                }
            
            } else {
                $_SESSION['error'] = "Registration failed. Try again.";
                header("Location: register.php");
                exit();
            }
        }
    }
} else {
    header("Location: register.php");
    exit();
}
?>



