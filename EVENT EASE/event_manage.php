<?php
session_start();
require_once __DIR__ . '/db.php';

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: signin.php");
    exit;
}

$organizer_id = $_SESSION['user_id'];

// Fetch events for this organizer
$stmt = $conn->prepare("SELECT * FROM events WHERE organizer_id = ? ORDER BY event_date DESC");
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$events = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Events | OETMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f0f2f5;
    padding: 40px 20px;
}
.table img {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
}
.table th, .table td {
    vertical-align: middle;
}
.btn-edit {
    background: #43c6ac;
    color: #fff;
}
.btn-edit:hover {
    background: #38ef7d;
    color: #fff;
}
.btn-delete {
    background: #ff4d4f;
    color: #fff;
}
.btn-delete:hover {
    background: #ff7875;
    color: #fff;
}
</style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">📅 My Events</h2>
    <a href="create_event.php" class="btn btn-success mb-3"><i class="fa-solid fa-plus"></i> Create New Event</a>
    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Date</th>
                <th>Location</th>
                <th>Price (KSH)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($events->num_rows > 0): ?>
            <?php while ($event = $events->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if (!empty($event['image'])): ?>
                        <img src="<?= htmlspecialchars($event['image']); ?>" alt="Event Image">
                    <?php else: ?>
                        <img src="default_event.png" alt="No Image">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($event['title']); ?></td>
                <td><?= htmlspecialchars($event['event_date']); ?></td>
                <td><?= htmlspecialchars($event['location']); ?></td>
                <td><?= htmlspecialchars(number_format($event['price'], 2)); ?></td>
                <td>
                    <a href="edit_event.php?id=<?= $event['id']; ?>" class="btn btn-sm btn-edit mb-1"><i class="fa-solid fa-edit"></i> Edit</a>
                    <a href="delete_event.php?id=<?= $event['id']; ?>" class="btn btn-sm btn-delete mb-1" onclick="return confirm('Are you sure you want to delete this event?');"><i class="fa-solid fa-trash"></i> Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No events found. Create one now!</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
