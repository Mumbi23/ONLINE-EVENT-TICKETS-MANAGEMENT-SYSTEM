<?php
// update_admin_profile.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $admin_id = $_SESSION['user_id'];

    if ($name && $email) {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $admin_id);
        $stmt->execute();

        // Log action
        $log = $conn->prepare("INSERT INTO audit_logs (user, action) VALUES (?, ?)");
        $action = "Updated profile details";
        $log->bind_param("ss", $_SESSION['username'], $action);
        $log->execute();

        header("Location: admin_dashboard.php?updated=1");
        exit();
    } else {
        echo "Please fill in all fields.";
    }
}
?>
