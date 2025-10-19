<?php
// view_reports.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit;
}

require_once __DIR__ . '/db.php';

// Fetch data
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
$usersByRole = $conn->query("SELECT role, COUNT(*) AS count FROM users GROUP BY role");

$totalEvents = $conn->query("SELECT COUNT(*) AS count FROM events")->fetch_assoc()['count'];
$eventsByOrganizer = $conn->query("
    SELECT u.name AS organizer, COUNT(e.id) AS total_events 
    FROM events e 
    JOIN users u ON e.organizer_id = u.id 
    GROUP BY e.organizer_id
");

$ticketsPerEvent = $conn->query("
    SELECT e.title, COUNT(t.id) AS tickets_sold
    FROM events e
    LEFT JOIN tickets t ON e.id = t.event_id
    GROUP BY e.id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Reports | OETMS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; }
.navbar { background: #1e2a38; }
.navbar-brand { color: #fff !important; font-weight: bold; }
table { background: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
th, td { padding: 12px 15px; }
th { background: #1e2a38; color: #fff; }
h3 { margin: 30px 0 20px; color: #1e2a38; }
.btn-back { margin-bottom: 20px; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Admin Reports</a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="btn btn-danger ms-2" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">

    <a href="admin_dashboard.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>

    <h3>Users Summary</h3>
    <p>Total Users: <strong><?= $totalUsers; ?></strong></p>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Role</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $usersByRole->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars(ucfirst($row['role'])); ?></td>
                <td><?= $row['count']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h3>Events Summary</h3>
    <p>Total Events: <strong><?= $totalEvents; ?></strong></p>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Organizer</th>
                <th>Events Organized</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $eventsByOrganizer->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['organizer']); ?></td>
                <td><?= $row['total_events']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h3>Tickets Sold Per Event</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Event Title</th>
                <th>Tickets Sold</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $ticketsPerEvent->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']); ?></td>
                <td><?= $row['tickets_sold']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
