<?php
// tickets_sold.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: signin.php");
    exit;
}

require_once __DIR__ . '/db.php';

$user_id = $_SESSION['user_id'];

// Fetch tickets sold for events created by this organizer
$sql = "
    SELECT 
        e.title AS event_title,
        e.event_date,
        e.location,
        t.id AS ticket_id,
        u.name AS attendee_name,
        u.email AS attendee_email,
        t.seat_number,
        p.amount AS payment_amount,
        p.status AS payment_status
    FROM tickets t
    JOIN events e ON t.event_id = e.id
    JOIN users u ON t.user_id = u.id
    LEFT JOIN payments p ON t.id = p.ticket_id
    WHERE e.organizer_id = ?
    ORDER BY e.event_date DESC, t.id ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tickets Sold | OETMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #1d2671, #c33764);
      min-height: 100vh;
      color: #fff;
    }
    .container {
      margin-top: 40px;
    }
    table {
      background: #fff;
      color: #000;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
    }
    footer {
      margin-top: 40px;
      text-align: center;
      padding: 15px;
      background: rgba(0,0,0,0.5);
    }
  </style>
</head>
<body>
    <nav>
    <div class="navbar navbar-expand-lg" style="background: #7024bcff; padding: 10px 20px;">
      <a class="navbar-brand text-white" href="#"><i class="fa-solid fa-ticket"></i> <b>Tickets Sold</b></a>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link text-white" href="organizer_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li class="nav-item"><a class="btn btn-danger ms-2" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
      </div>
    </nav>

<div class="container">

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Event</th>
        <th>Date</th>
        <th>Location</th>
        <th>Attendee</th>
        <th>Email</th>
        <th>Seat</th>
        <th>Payment</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['event_title']); ?></td>
            <td><?= htmlspecialchars($row['event_date']); ?></td>
            <td><?= htmlspecialchars($row['location']); ?></td>
            <td><?= htmlspecialchars($row['attendee_name']); ?></td>
            <td><?= htmlspecialchars($row['attendee_email']); ?></td>
            <td><?= htmlspecialchars($row['seat_number'] ?? 'N/A'); ?></td>
            <td>$<?= number_format($row['payment_amount'] ?? 0, 2); ?></td>
            <td>
              <?php if ($row['payment_status'] === 'paid'): ?>
                <span class="badge bg-success">Paid</span>
              <?php else: ?>
                <span class="badge bg-danger">Unpaid</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="8" class="text-center">No tickets sold yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include 'templates/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
