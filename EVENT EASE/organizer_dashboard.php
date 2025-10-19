<?php
// organizer_dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (empty($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: signin.php");
    exit;
}

require_once __DIR__ . '/db.php';

$user_id = $_SESSION['user_id'];

// Organizer stats
$stats = $conn->query("
    SELECT 
        IFNULL(SUM(p.amount), 0) AS total_revenue,
        COUNT(DISTINCT t.id) AS total_tickets,
        COUNT(DISTINCT e.id) AS total_events
    FROM events e
    LEFT JOIN tickets t ON e.id = t.event_id
    LEFT JOIN payments p ON e.id = p.event_id
    WHERE e.organizer_id = $user_id
")->fetch_assoc();

// Organizer events
$events = $conn->query("
    SELECT e.id, e.title, e.event_date, e.location,
           COUNT(DISTINCT t.id) AS tickets_sold,
           IFNULL(SUM(p.amount), 0) AS revenue
    FROM events e
    LEFT JOIN tickets t ON e.id = t.event_id
    LEFT JOIN payments p ON e.id = p.event_id
    WHERE e.organizer_id = $user_id
    GROUP BY e.id
    ORDER BY e.event_date DESC
");

// Handle delete request
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id=? AND organizer_id=?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Event deleted successfully!";
        header("Location: organizer_dashboard.php");
        exit;
    } else {
        $_SESSION['message'] = "Error deleting event.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Organizer Dashboard | OETMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #1d2671, #c33764);
      min-height: 100vh;
      color: #fff;
      overflow-x: hidden;
    }

    /* Sidebar */
    .sidebar {
      width: 240px;
      background: linear-gradient(180deg, #667eea, #764ba2);
      color: #fff;
      padding: 20px;
      position: fixed;
      top: 0; bottom: 0; left: 0;
      box-shadow: 3px 0 10px rgba(0,0,0,0.3);
    }
    .sidebar h3 {
      font-weight: bold;
      margin-bottom: 30px;
    }
    .sidebar a {
      display: block;
      padding: 12px 15px;
      color: #fff;
      border-radius: 8px;
      text-decoration: none;
      margin-bottom: 8px;
      transition: background 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background: rgba(255, 255, 255, 0.2);
    }

    /* Main Content */
    .content {
      margin-left: 260px;
      padding: 30px;
    }

    .dashboard-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .alert {
      max-width: 700px;
      margin: 0 auto 25px;
      opacity: 0.95;
      transition: opacity 0.6s ease-out;
    }

    /* Stats */
    .stats-card {
      background: rgba(255,255,255,0.1);
      border-radius: 15px;
      padding: 20px;
      text-align: center;
      transition: transform 0.3s;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }
    .stats-card:hover { transform: translateY(-5px); }
    .stats-card i { font-size: 2rem; margin-bottom: 10px; }

    /* Buttons */
    .btn-create {
      background: #ffd200;
      color: #000;
      font-weight: 600;
      border-radius: 25px;
      transition: 0.3s;
    }
    .btn-create:hover {
      background: #ffc107;
      color: #fff;
    }
    .btn-delete {
      background: #dc3545;
      color: #fff;
    }
    .btn-delete:hover {
      background: #b02a37;
      color: #fff;
    }
    .btn-view {
      background: #17a2b8;
      color: #fff;
    }
    .btn-edit {
      background: #ffc107;
      color: #000;
    }

    /* Table */
    .table-container {
      width: 100%;
      overflow-x: auto;
    }
    table {
      width: 100%;
      min-width: 950px;
      background: #fff;
      color: #000;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    table th, table td {
      vertical-align: middle !important;
      text-align: center;
      white-space: nowrap;
    }
    .action-btns {
      display: flex;
      justify-content: center;
      gap: 5px;
    }
    .action-btns .btn {
      font-size: 0.85rem;
      padding: 4px 8px;
    }

    footer {
      margin-top: 40px;
      text-align: center;
      padding: 20px;
      background: rgba(0,0,0,0.5);
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h3><i class="fa-solid fa-ticket"></i> OETMS</h3>
  <a href="organizer_dashboard.php" class="active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
  <a href="tickets_sold.php"><i class="fa-solid fa-ticket"></i> Tickets Sold</a>
  <a href="organizer_reports.php"><i class="fa-solid fa-calendar-days"></i> Reports</a>
  <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
  <a href="contact.php"><i class="fa-solid fa-info-circle"></i> Support</a>
  <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">
  <div class="dashboard-header">
    <h2>👋 Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Organizer'); ?></h2>
    <p>Manage your events and track performance</p>
    <a href="create_event.php" class="btn btn-create mt-3"><i class="fa-solid fa-plus"></i> Create New Event</a>
  </div>

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success text-center">
      <?= htmlspecialchars($_SESSION['message']); ?>
    </div>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>

  <!-- Stats -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="stats-card">
        <i class="fa-solid fa-calendar-days"></i>
        <h4><?= $stats['total_events']; ?></h4>
        <p>Events Created</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stats-card">
        <i class="fa-solid fa-ticket"></i>
        <h4><?= $stats['total_tickets']; ?></h4>
        <p>Tickets Sold</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stats-card">
        <i class="fa-solid fa-dollar-sign"></i>
        <h4>$<?= number_format($stats['total_revenue'], 2); ?></h4>
        <p>Total Revenue</p>
      </div>
    </div>
  </div>

  <!-- Events Table -->
  <div class="table-container">
    <h4 class="mb-3">📌 Your Events</h4>
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>Title</th>
          <th>Date</th>
          <th>Location</th>
          <th>Tickets Sold</th>
          <th>Revenue</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($events->num_rows > 0): ?>
          <?php while($event = $events->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($event['title']); ?></td>
              <td><?= htmlspecialchars($event['event_date']); ?></td>
              <td><?= htmlspecialchars($event['location']); ?></td>
              <td><?= $event['tickets_sold']; ?></td>
              <td>$<?= number_format($event['revenue'], 2); ?></td>
              <td>
                <div class="action-btns">
                  <a href="event.php?id=<?= $event['id']; ?>" class="btn btn-sm btn-view">
                    <i class="fa-solid fa-eye"></i> View
                  </a>
                  <a href="edit_event.php?id=<?= $event['id']; ?>" class="btn btn-sm btn-edit">
                    <i class="fa-solid fa-pen"></i> Edit
                  </a>
                  <button type="button" class="btn btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $event['id']; ?>">
                    <i class="fa-solid fa-trash"></i> Delete
                  </button>
                </div>
              </td>
            </tr>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal<?= $event['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $event['id']; ?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel<?= $event['id']; ?>">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body text-dark">
                    Are you sure you want to delete <strong><?= htmlspecialchars($event['title']); ?></strong>?  
                    This action cannot be undone.
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" class="d-inline">
                      <input type="hidden" name="delete_id" value="<?= $event['id']; ?>">
                      <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center">No events created yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<footer>
  <p>&copy; <?= date("Y") ?> OETMS | All Rights Reserved</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  setTimeout(() => {
    const alertBox = document.querySelector('.alert');
    if (alertBox) {
      alertBox.style.opacity = '0';
      setTimeout(() => alertBox.remove(), 600);
    }
  }, 3500);
</script>
</body>
</html>
