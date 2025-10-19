<?php
// update_notifications.php
// ==========================================
// Marks all notifications as "read" for the
// currently logged-in user in OETMS.
// ==========================================

session_start();
require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/notify.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Mark all notifications as read
mark_notifications_as_read($conn, $user_id);

// Redirect back to notifications or dashboard
header("Location: notifications.php");
exit;
?>
