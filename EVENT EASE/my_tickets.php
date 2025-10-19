<?php
// my_tickets.php - Displays tickets purchased by the logged-in attendee
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include 'db.php';

// Only attendees can access their tickets
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'attendee') {
  header("Location: signin.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch tickets joined with event details
$stmt = $conn->prepare("
  SELECT tickets.id, events.title, events.event_date, events.location, 
         tickets.seat_number, tickets.quantity, tickets.purchase_date
  FROM tickets
  JOIN events ON tickets.event_id = events.id
  WHERE tickets.user_id=?
  ORDER BY tickets.purchase_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Tickets | OETMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
    nav.navbar { background: #198754; }
    nav.navbar a.nav-link, .navbar-brand { color: #fff !important; }
    .ticket-card {
      border: 2px dashed #198754;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 20px;
      background: #fff;
      position: relative;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .ticket-card::before, .ticket-card::after {
      content: "";
      position: absolute;
      width: 20px; height: 20px;
      background: #f4f6f9;
      border-radius: 50%;
      top: 50%; transform: translateY(-50%);
    }
    .ticket-card::before { left: -12px; }
    .ticket-card::after { right: -12px; }
    .ticket-header { border-bottom: 1px solid #ccc; margin-bottom: 12px; }
    .ticket-actions { margin-top: 10px; }
    .back-btn { display: block; text-align: center; margin: 30px 0; }
    .back-btn a { text-decoration: none; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">OETMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="attendee_dashboard.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="browse_events.php">Browse Events</a></li>
        <li class="nav-item"><a class="nav-link active fw-bold" href="my_tickets.php">My Tickets</a></li>
        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-4">
  <h2 class="fw-bold text-success mb-4">
    <i class="fas fa-ticket-alt"></i> My Tickets
  </h2>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="ticket-card">
        <div class="ticket-header">
          <h4 class="text-success fw-bold"><?= htmlspecialchars($row['title']) ?></h4>
          <small class="text-muted">Purchased on <?= date("M d, Y", strtotime($row['purchase_date'])) ?></small>
        </div>
        <p><i class="fas fa-calendar-check text-success"></i> <strong>Date:</strong> <?= date("M d, Y", strtotime($row['event_date'])) ?></p>
        <p><i class="fas fa-map-marker-alt text-danger"></i> <strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
        <?php if (!empty($row['seat_number'])): ?>
          <p><i class="fas fa-chair text-primary"></i> <strong>Seat:</strong> <?= htmlspecialchars($row['seat_number']) ?></p>
        <?php endif; ?>
        <?php if (!empty($row['quantity'])): ?>
          <p><i class="fas fa-list-ol text-info"></i> <strong>Quantity:</strong> <?= (int)$row['quantity'] ?></p>
        <?php endif; ?>
        <div class="ticket-actions">
          <a href="download_ticket.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-success">
            <i class="fas fa-download"></i> Download PDF
          </a>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="alert alert-info">
      <i class="fas fa-info-circle"></i> You haven’t purchased any tickets yet.
      <a href="browse_events.php" class="btn btn-sm btn-outline-primary ms-2">
        <i class="fas fa-search"></i> Browse Events
      </a>
    </div>
  <?php endif; ?>

  

<footer class="text-center text-muted py-3">
  &copy; <?= date("Y") ?> OETMS | All Rights Reserved
</footer>
<!-- Back to Dashboard Button -->
  <div class="back-btn">
    <a href="attendee_dashboard.php" class="btn btn-secondary btn-lg">
      &larr; Back to Dashboard
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
