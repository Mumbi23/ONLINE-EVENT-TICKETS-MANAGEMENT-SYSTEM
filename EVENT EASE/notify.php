<?php
// src/notify.php
// ======================================================
// Helper functions to create and manage notifications
// for the Online Event Ticket Management System (OETMS).
// ======================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/**
 * Send a notification to a specific user.
 *
 * @param mysqli $conn       The database connection.
 * @param int    $user_id    The ID of the user receiving the notification.
 * @param string $message    The notification message.
 * @param string $type       The notification type (info, success, warning, error).
 */
function send_notification($conn, $user_id, $message, $type = 'info') {
    if (empty($user_id) || empty($message)) {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $message, $type);
    $stmt->execute();
    $stmt->close();

    return true;
}

/**
 * Get all notifications for a user (most recent first).
 *
 * @param mysqli $conn
 * @param int $user_id
 * @param int $limit
 * @return mysqli_result
 */
function get_notifications($conn, $user_id, $limit = 10) {
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

/**
 * Count unread notifications for a user.
 *
 * @param mysqli $conn
 * @param int $user_id
 * @return int
 */
function count_unread_notifications($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS unread FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['unread'] ?? 0;
}

/**
 * Mark all notifications as read for a user.
 *
 * @param mysqli $conn
 * @param int $user_id
 * @return void
 */
function mark_notifications_as_read($conn, $user_id) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}
?>
