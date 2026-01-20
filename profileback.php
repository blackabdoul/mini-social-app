<?php
session_start();
require_once "config.php";

// Check if user is logged in
if(!isset($_SESSION["id_user"])){
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION["id_user"];

    // Update Personal Information
    if ($action === 'update_info') {
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $dob = $_POST['dob'] ?? null;

        try {
            $sql = "UPDATE users SET 
                    full_name = :full_name,
                    phone = :phone,
                    bio = :bio,
                    location = :location,
                    dob = :dob,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':full_name', $fullName, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Profile updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update profile.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }

        header("Location: profile.php");
        exit();
    }

    // Change Password
    elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$currentPassword || !$newPassword || !$confirmPassword) {
            $_SESSION['error'] = "All password fields are required.";
            header("Location: profile.php");
            exit();
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = "New passwords do not match.";
            header("Location: profile.php");
            exit();
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = "New password must be at least 6 characters.";
            header("Location: profile.php");
            exit();
        }

        // Verify current password
        try {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = "Current password is incorrect.";
                header("Location: profile.php");
                exit();
            }

            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id");
            $updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $updateStmt->bindParam(':id', $userId, PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                $_SESSION['success'] = "Password changed successfully!";
            } else {
                $_SESSION['error'] = "Failed to change password.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }

        header("Location: profile.php");
        exit();
    }

    // Delete Account
    elseif ($action === 'delete_account') {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                session_destroy();
                $_SESSION['success'] = "Account deleted successfully";
                header("Location: index.php?account_deleted=1");
                exit();
            } else {
                $_SESSION['error'] = "Failed to delete account.";
                header("Location: profile.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            header("Location: profile.php");
            exit();
        }
    }

    else {
        $_SESSION['error'] = "Invalid action.";
        header("Location: profile.php");
        exit();
    }
} else {
    header("Location: profile.php");
    exit();
}
?>