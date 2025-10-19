<?php
// admin_dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Restrict to admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

require_once __DIR__ . '/db.php';

// --- Dashboard Stats ---
$totalRevenue = $conn->query("SELECT IFNULL(SUM(amount),0) AS revenue FROM payments")->fetch_assoc()['revenue'];
$totalTickets = $conn->query("SELECT COUNT(*) AS c FROM tickets")->fetch_assoc()['c'];
$totalEvents = $conn->query("SELECT COUNT(*) AS c FROM events")->fetch_assoc()['c'];
$totalOrganizers = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='organizer'")->fetch_assoc()['c'];

// --- Recent Events ---
$recentEvents = $conn->query("
    SELECT e.id, e.title, e.event_date, COUNT(t.id) AS sold, IFNULL(SUM(p.amount),0) AS rev
    FROM events e
    LEFT JOIN tickets t ON e.id = t.event_id
    LEFT JOIN payments p ON t.id = p.ticket_id
    GROUP BY e.id
    ORDER BY e.event_date DESC LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// --- Chart Data ---
$eventNames = [];
$ticketSales = [];
$result = $conn->query("
    SELECT e.title, COUNT(t.id) AS sold
    FROM events e
    LEFT JOIN tickets t ON e.id = t.event_id
    GROUP BY e.id
    ORDER BY sold DESC LIMIT 5
");
while ($row = $result->fetch_assoc()) {
    $eventNames[] = $row['title'];
    $ticketSales[] = $row['sold'];
}

// --- Manage Users ---
$users = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

// --- System Logs ---
$auditLogs = $conn->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Event Ticket System</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    background: #f4f6f9;
}

/* Sidebar styling */
.sidebar {
    width: 230px;
    min-height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: #111827;
    color: #fff;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.sidebar h4 {
    text-align: center;
    margin-bottom: 30px;
    font-weight: bold;
    color: #38bdf8;
}
.sidebar .nav {
    flex-grow: 1;
}
.sidebar .nav-link {
    color: #fff;
    padding: 10px 15px;
    border-radius: 6px;
    display: block;
    margin-bottom: 5px;
    transition: background 0.3s;
}
.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background: rgba(255, 255, 255, 0.15);
    text-decoration: none;
}
.sidebar .logout-link {
    background: #b91c1c;
    color: #fff !important;
    font-weight: bold;
    text-align: center;
    margin-top: 15px;
    border-radius: 8px;
    transition: background 0.3s;
}
.sidebar .logout-link:hover {
    background: #dc2626;
    color: #fff;
}

/* Main content */
.main-content {
    margin-left: 250px;
    padding: 25px;
}
.card {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.table-responsive {
    max-height: 400px;
    overflow-y: auto;
}
.chart-container {
    margin: 20px 0;
}
.section {
    display: none;
}
.section.active {
    display: block;
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div>
    <h4>🎟️ Event Admin</h4>
    <ul class="nav flex-column">
      <li><a class="nav-link active" href="#" data-section="dashboard">Dashboard</a></li>
      <li><a class="nav-link" href="#" data-section="users">Manage Users</a></li>
      <li><a class="nav-link" href="admin_report.php" data-section="reports">Reports</a></li>
      <li><a class="nav-link" href="#" data-section="settings">Settings & Security</a></li>
      <li><a class="nav-link" href="#" data-section="maintenance">System Maintenance</a></li>
    </ul>
  </div>
  <a class="nav-link logout-link" href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">

  <!-- DASHBOARD -->
  <div id="dashboard" class="section active">
    <h2>📊 Admin Dashboard</h2>
    <div class="row g-4 mt-3">
      <div class="col-md-3"><div class="card text-center p-3"><h6>Total Revenue</h6><h3 class="text-success">$<?= number_format($totalRevenue,2) ?></h3></div></div>
      <div class="col-md-3"><div class="card text-center p-3"><h6>Total Tickets Sold</h6><h3 class="text-primary"><?= $totalTickets ?></h3></div></div>
      <div class="col-md-3"><div class="card text-center p-3"><h6>Total Events</h6><h3 class="text-info"><?= $totalEvents ?></h3></div></div>
      <div class="col-md-3"><div class="card text-center p-3"><h6>Total Organizers</h6><h3 class="text-warning"><?= $totalOrganizers ?></h3></div></div>
    </div>

    <div class="chart-container mt-4">
      <div class="card p-3">
        <h5>Top 5 Events by Ticket Sales</h5>
        <canvas id="ticketChart"></canvas>
      </div>
    </div>

    <div class="card p-3 mt-4">
      <h5>Recent Events</h5>
      <table class="table table-striped">
        <thead><tr><th>Event</th><th>Date</th><th>Status</th><th>Tickets Sold</th><th>Revenue</th></tr></thead>
        <tbody>
          <?php foreach ($recentEvents as $r):
              $eventDate = strtotime($r['event_date']);
              $today = strtotime(date("Y-m-d"));
              $status = $eventDate > $today ? "<span class='badge bg-success'>Upcoming</span>" :
                        ($eventDate == $today ? "<span class='badge bg-warning text-dark'>Ongoing</span>" :
                        "<span class='badge bg-danger'>Completed</span>");
          ?>
          <tr>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['event_date']) ?></td>
            <td><?= $status ?></td>
            <td><?= $r['sold'] ?></td>
            <td>$<?= number_format($r['rev'],2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- MANAGE USERS -->
  <div id="users" class="section">
    <h2>👥 Manage Users</h2>
    <div class="card p-3 mt-3">
      <input type="text" id="userSearch" placeholder="Search users..." class="form-control mb-3">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead>
          <tbody id="userTable">
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= ucfirst($u['role']) ?></td>
                <td><?= $u['created_at'] ?></td>
                <td>
                  <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                  <a href="delete_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- SETTINGS -->
  <div id="settings" class="section">
    <h2>⚙️ Settings & Security</h2>
    <div class="row mt-3">
      <div class="col-md-6">
        <div class="card p-3">
          <h5>Admin Profile</h5>
          <form method="post" action="update_admin_profile.php">
            <input type="text" name="name" class="form-control mb-2" placeholder="Full Name" required>
            <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
            <button type="submit" class="btn btn-success w-100">Update Profile</button>
          </form>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-3">
          <h5>Change Password</h5>
          <form method="post" action="update_password.php">
            <input type="password" name="old_pass" class="form-control mb-2" placeholder="Current Password" required>
            <input type="password" name="new_pass" class="form-control mb-2" placeholder="New Password" required>
            <button type="submit" class="btn btn-warning w-100">Change Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- SYSTEM MAINTENANCE -->
  <div id="maintenance" class="section">
    <h2>🛠️ System Maintenance</h2>
    <div class="row mt-3">
      <div class="col-md-6">
        <div class="card p-3">
          <h5>Backup Database</h5>
          <p>Download a full SQL backup of your system data.</p>
          <a href="backup.php" class="btn btn-secondary w-100">Run Backup</a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-3">
          <h5>Audit Logs</h5>
          <div class="table-responsive" style="max-height:250px;">
            <table class="table table-striped">
              <thead><tr><th>User</th><th>Action</th><th>Date</th></tr></thead>
              <tbody>
              <?php foreach ($auditLogs as $log): ?>
                <tr>
                  <td><?= htmlspecialchars($log['user']) ?></td>
                  <td><?= htmlspecialchars($log['action']) ?></td>
                  <td><?= htmlspecialchars($log['created_at']) ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript -->
<script>
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', e => {
    // Allow logout to work normally
    if (link.classList.contains('logout-link')) return;

    e.preventDefault();
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    link.classList.add('active');
    const section = link.dataset.section;
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById(section).classList.add('active');
  });
});

// Chart.js
const ctx = document.getElementById('ticketChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($eventNames) ?>,
    datasets: [{
      label: 'Tickets Sold',
      data: <?= json_encode($ticketSales) ?>,
      backgroundColor: '#3b82f6'
    }]
  },
  options: { responsive:true, scales:{ y:{ beginAtZero:true } } }
});

// Filter users
document.getElementById('userSearch').addEventListener('keyup', function(){
  const search = this.value.toLowerCase();
  document.querySelectorAll('#userTable tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
  });
});
</script>

</body>
</html>
