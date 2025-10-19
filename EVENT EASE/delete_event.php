<?php
// delete_event.php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: signin.php");
    exit;
}

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Verify event ownership
    $check = $conn->prepare("SELECT id FROM events WHERE id = ? AND organizer_id = ?");
    $check->bind_param("ii", $event_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Delete event and related records
        $conn->query("DELETE FROM tickets WHERE event_id = $event_id");
        $conn->query("DELETE FROM payments WHERE event_id = $event_id");
        $conn->query("DELETE FROM events WHERE id = $event_id");
        $_SESSION['message'] = "Event deleted successfully!";
    } else {
        $_SESSION['message'] = "You are not authorized to delete this event.";
    }
}

header("Location: organizer_dashboard.php");
exit;
?>