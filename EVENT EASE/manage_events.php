<?php
// manage_events.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit;
}

require_once __DIR__ . '/db.php';

// Fetch all events
$events = $conn->query("SELECT id, title, event_date, location, created_at FROM events ORDER BY event_date ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Events | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4">📅 Manage Events</h2>
  <a href="add_event.php" class="btn btn-success mb-3">➕ Add Event</a>
  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Date</th>
        <th>Location</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while($e = $events->fetch_assoc()): ?>
      <tr>
        <td><?= $e['id']; ?></td>
        <td><?= htmlspecialchars($e['title']); ?></td>
        <td><?= $e['event_date']; ?></td>
        <td><?= htmlspecialchars($e['location']); ?></td>
        <td><?= $e['created_at']; ?></td>
        <td>
          <a href="edit_event.php?id=<?= $e['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="delete_event.php?id=<?= $e['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this event?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
       <div class="d-flex justify-content-between">
   <a href="javascript:history.back()" class="btn btn-secondary btn-back">
  <i class="fa fa-arrow-left"></i> Back
</a>
</div>
</body>
</html>
