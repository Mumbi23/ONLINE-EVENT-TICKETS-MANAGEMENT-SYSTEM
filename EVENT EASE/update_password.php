<?php
// admin_password_change.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_SESSION['user_id'];
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];

    // Fetch current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($current_hash);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($old_pass, $current_hash)) {
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $update->bind_param("si", $new_hash, $admin_id);
        $update->execute();

        // Add to audit log
        $log = $conn->prepare("INSERT INTO audit_logs (user, action) VALUES (?, ?)");
        $action = "Changed admin password";
        $log->bind_param("ss", $_SESSION['name'], $action);
        $log->execute();

        header("Location: admin_dashboard.php?passchanged=1");
        exit();
    } else {
        echo "<script>alert('Incorrect current password'); window.history.back();</script>";
    }
}
?>
