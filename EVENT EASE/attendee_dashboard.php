<?php
// attendee_dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (empty($_SESSION['role']) || $_SESSION['role'] !== 'attendee') {
    header("Location: signin.php");
    exit;
}

require_once __DIR__ . '/db.php';

// Fetch stats
$user_id = $_SESSION['user_id'];
$totalTickets = $conn->query("SELECT COUNT(*) AS count FROM tickets WHERE user_id = $user_id")->fetch_assoc()['count'];

$upcomingEvents = $conn->query("
    SELECT e.id, e.title, e.event_date AS date, e.location AS venue, t.id AS ticket_id
    FROM events e
    JOIN tickets t ON e.id = t.event_id
    WHERE t.user_id = $user_id AND e.event_date >= CURDATE()
    ORDER BY e.event_date ASC
");

$upcomingCount = $upcomingEvents->num_rows;

$pendingPayments = $conn->query("
    SELECT COUNT(*) AS count 
    FROM payments 
    WHERE user_id = $user_id AND status = 'pending'
")->fetch_assoc()['count'];

// Fetch attendee name if not set in session
if (empty($_SESSION['name'])) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $_SESSION['name'] = $user ? $user['name'] : "Attendee";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendee Dashboard | OETMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f7f9fc;
      min-height: 100vh;
      display: flex;
    }
    /* Sidebar */
    .sidebar {
      width: 240px;
      background: linear-gradient(180deg, #667eea, #764ba2);
      color: #fff;
      padding: 20px;
      position: fixed;
      top: 0; bottom: 0; left: 0;
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
    .main {
      margin-left: 240px;
      padding: 30px;
      width: 100%;
    }

    .dashboard-header h2 { font-weight: bold; color: #333; }
    .dashboard-header p { color: #666; }

    /* Stats Cards */
    .stats-card {
      background: #fff;
      border-radius: 16px;
      padding: 25px;
      text-align: center;
      box-shadow: 0 6px 15px rgba(0,0,0,0.08);
      transition: transform 0.3s;
    }
    .stats-card:hover { transform: translateY(-5px); }
    .stats-card i {
      font-size: 2rem;
      margin-bottom: 10px;
      color: #667eea;
    }
    .stats-card h4 { font-size: 2rem; font-weight: bold; }
    .stats-card p { margin: 0; color: #555; }

    /* Upcoming Events */
    .event-card {
      background: #fff;
      border-radius: 14px;
      padding: 20px;
      box-shadow: 0 5px 12px rgba(0,0,0,0.06);
      transition: all 0.3s ease;
    }
    .event-card:hover { transform: translateY(-4px); }
    .event-card h5 { font-weight: 600; color: #333; }
    .event-card p { margin-bottom: 6px; color: #666; }
    .countdown { font-weight: bold; color: #d9534f; }

    footer {
      background: #222;
      color: #aaa;
      text-align: center;
      padding: 20px;
      margin-top: 40px;
      border-radius: 10px;
    }
  </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
  <h3><i class="fa-solid fa-ticket"></i>Event Ease</h3>
  <a href="attendee_dashboard.php" class="active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
  <a href="browse_events.php"><i class="fa-solid fa-calendar-days"></i> Browse Events</a>
  <a href="my_tickets.php"><i class="fa-solid fa-ticket"></i> My Tickets</a>
  <li><a href="payment.php"><i class="fas fa-wallet"></i> My Payments</a></li>
  <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
  <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main">
  <!-- Header -->
  <div class="dashboard-header mb-4" style ="text-align: center;">
    <h2>👋 Welcome, <?= htmlspecialchars($_SESSION['name']); ?>!</h2>
    <p>Here’s what’s happening with your events</p>
  </div>

  <!-- Stats Section -->
  <div class="row text-center mb-5">
    <div class="col-md-4">
      <div class="stats-card">
        <i class="fa-solid fa-ticket"></i>
        <h4><?= $totalTickets; ?></h4>
        <p>Tickets Booked</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stats-card">
        <i class="fa-solid fa-calendar-days"></i>
        <h4><?= $upcomingCount; ?></h4>
        <p>Upcoming Events</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stats-card">
        <i class="fa-solid fa-credit-card"></i>
        <h4><?= $pendingPayments; ?></h4>
        <p>Pending Payments</p>
      </div>
    </div>
  </div>

  <!-- Upcoming Events -->
  <h4 class="mb-3">⏰ Your Upcoming Events</h4>
  <div class="row">
    <?php if ($upcomingCount > 0): ?>
      <?php while($row = $upcomingEvents->fetch_assoc()): ?>
      <div class="col-md-4 mb-3">
        <div class="event-card">
          <h5><?= htmlspecialchars($row['title']); ?></h5>
          <p><i class="fa-solid fa-calendar"></i> <?= htmlspecialchars($row['date']); ?></p>
          <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($row['venue']); ?></p>
          <p class="countdown" data-event-date="<?= htmlspecialchars($row['date']); ?>">⏳ Loading...</p>
          <a href="ticket.php?id=<?= $row['ticket_id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-ticket"></i> Ticket</a>
          <a href="event.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-circle-info"></i> Details</a>
        </div>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-muted">No upcoming events yet. <a href="browse_events.php" class="btn btn-outline-primary btn-sm">Browse Events</a></p>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <footer>
    <p>&copy; <?= date("Y") ?> OETMS | All Rights Reserved</p>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Countdown Timer
document.querySelectorAll('.countdown').forEach(function(el) {
  let eventDate = new Date(el.getAttribute('data-event-date')).getTime();

  let timer = setInterval(function() {
    let now = new Date().getTime();
    let distance = eventDate - now;

    if (distance <= 0) {
      el.innerHTML = "🎉 Event Started!";
      clearInterval(timer);
    } else {
      let days = Math.floor(distance / (1000 * 60 * 60 * 24));
      let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      let seconds = Math.floor((distance % (1000 * 60)) / 1000);

      el.innerHTML = `⏳ ${days}d ${hours}h ${minutes}m ${seconds}s`;
    }
  }, 1000);
});
</script>
</body>
</html>
