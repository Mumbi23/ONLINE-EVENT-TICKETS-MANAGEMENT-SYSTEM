<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../db.php';
$username = "User";

// Fetch user name if logged in
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $username = $user ? $user['name'] : "User";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OETMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body { margin: 0; font-family: Arial, sans-serif; }

    /* Top bar */
    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #2c3e50;
      color: #fff;
      padding: 10px 20px;
    }
    .topbar h1 { font-size: 20px; margin: 0; }
    .menu-toggle { font-size: 24px; cursor: pointer; }
    .user-info { font-size: 14px; }

    /* Sidebar (RIGHT SIDE) */
    .sidebar {
      height: 100%;
      width: 0;
      position: fixed;
      top: 0;
      right: 0;
      background-color: #34495e;
      overflow-x: hidden;
      transition: 0.3s;
      padding-top: 60px;
      z-index: 999;
    }
    .sidebar a {
      padding: 15px 25px;
      text-decoration: none;
      font-size: 18px;
      color: #ecf0f1;
      display: block;
      transition: 0.2s;
    }
    .sidebar a:hover {
      background-color: #1abc9c;
      color: #fff;
    }
    .sidebar .close-btn {
      position: absolute;
      top: 10px;
      left: 20px;
      font-size: 28px;
    }
    .content {
      padding: 20px;
    }
  </style>
</head>
<body>

<!-- Top bar -->
<div class="topbar">
  <h1><i class="fas fa-ticket-alt text-warning"></i> EVENT EASE</h1>

  <div class="d-flex align-items-center gap-3">
    <?php if (isset($_SESSION['user_id'])): ?>
      <span class="user-info">
        <i class="fas fa-user-circle text-info"></i>
        👋 Hi, <strong><?= htmlspecialchars($username) ?></strong> (<?= ucfirst($_SESSION['role']) ?>)
      </span>
    <?php endif; ?>
    <span class="menu-toggle" onclick="openNav()"><i class="fas fa-bars"></i></span>
  </div>
</div>

<!-- Sidebar nav -->
<div id="sidebar" class="sidebar">
  <a href="javascript:void(0)" class="close-btn" onclick="closeNav()">&times;</a>

  <?php if (isset($_SESSION['user_id'])): ?>
    <?php if ($_SESSION['role'] === 'organizer'): ?>
      <a href="organizer_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="create_event.php"><i class="fas fa-plus-circle"></i> Create Event</a>
      <a href="manage_tickets.php"><i class="fas fa-ticket-alt"></i> Manage Tickets</a>
    <?php elseif ($_SESSION['role'] === 'attendee'): ?>
      <a href="attendee_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
      <a href="browse_events.php"><i class="fas fa-search"></i> Browse Events</a>
      <a href="my_tickets.php"><i class="fas fa-ticket-alt"></i> My Tickets</a>
    <?php endif; ?>

    <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
  <?php else: ?>
    <a href="signup.php"><i class="fas fa-user-plus"></i> Sign Up</a>
    <a href="signin.php"><i class="fas fa-sign-in-alt"></i> Sign In</a>
  <?php endif; ?>
</div>

<!-- Page content wrapper -->
<div class="content container" style="margin-top: 20px; text-align: center; color: #0c0c0cff; scroll-behavior: smooth;">
  <!-- Your page-specific content goes here -->
  <h2 class="mt-4">Welcome to EVENT EASE 🎉</h2>
</div>

<!-- Sidebar Toggle JS -->
<script>
  function openNav() {
    document.getElementById("sidebar").style.width = "250px";
  }
  function closeNav() {
    document.getElementById("sidebar").style.width = "0";
  }
</script>

</body>
</html>
