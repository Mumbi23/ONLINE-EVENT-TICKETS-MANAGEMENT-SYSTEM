<?php
// delete_user_action.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

require_once __DIR__ . '/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    exit('Invalid request');
}

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

http_response_code(200);
echo "User deleted successfully";
