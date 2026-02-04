<?php
require_once "admin-check.php"; // Admin only

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';
    $userId = $_POST['user_id'] ?? 0;
    
    // Delete User
    if ($action === 'delete_user') {
        try {
            // Can't delete yourself
            if ($userId == $_SESSION["id_user"]) {
                $_SESSION['error'] = "You cannot delete yourself.";
                header("Location: admin-dashboard.php");
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "User deleted successfully.";
            } else {
                $_SESSION['error'] = "Failed to delete user.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }
        
        header("Location: admin-dashboard.php");
        exit();
    }
    
    // Toggle Role (admin <-> user)
    elseif ($action === 'toggle_role') {
        try {
            // Can't change your own role
            if ($userId == $_SESSION["id_user"]) {
                $_SESSION['error'] = "You cannot change your own role.";
                header("Location: admin-dashboard.php");
                exit();
            }
            
            // Get current role
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                $_SESSION['error'] = "User not found.";
                header("Location: admin-dashboard.php");
                exit();
            }
            
            // Toggle role
            $newRole = $user['role'] === 'admin' ? 'user' : 'admin';
            
            $updateStmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
            $updateStmt->bindParam(':role', $newRole, PDO::PARAM_STR);
            $updateStmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            if ($updateStmt->execute()) {
                $_SESSION['success'] = "User role updated to: " . $newRole;
            } else {
                $_SESSION['error'] = "Failed to update role.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }
        
        header("Location: admin-dashboard.php");
        exit();
    }
    
    else {
        $_SESSION['error'] = "Invalid action.";
        header("Location: admin-dashboard.php");
        exit();
    }
} else {
    header("Location: admin-dashboard.php");
    exit();
}
?>