<?php
// Include this file in admin pages to restrict access

session_start();
require_once "config.php";

// Check if logged in
if (!isset($_SESSION["id_user"])) {
    header("Location: index.php");
    exit();
}

// Check if admin
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id LIMIT 1");
$stmt->bindParam(':id', $_SESSION["id_user"], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied. Admin only.";
    header("Location: dashboard.php");
    exit();
}
?>