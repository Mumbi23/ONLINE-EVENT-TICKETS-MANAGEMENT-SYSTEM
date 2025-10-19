<?php
// organizer_reports.php
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ Only organizers allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: signin.php");
    exit();
}

require_once __DIR__ . '/db.php';

// Logged-in organizer ID
$organizerId = $_SESSION['user_id'];

// --- Organizer Statistics ---
$stmt = $conn->prepare("SELECT IFNULL(SUM(p.amount),0) as rev 
                        FROM payments p
                        JOIN tickets t ON p.ticket_id = t.id
                        JOIN events e ON t.event_id = e.id
                        WHERE e.organizer_id=?");
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$totalRevenue = $stmt->get_result()->fetch_assoc()['rev'] ?? 0;

$stmt = $conn->prepare("SELECT COUNT(t.id) as c 
                        FROM tickets t 
                        JOIN events e ON t.event_id=e.id 
                        WHERE e.organizer_id=?");
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$totalTickets = $stmt->get_result()->fetch_assoc()['c'] ?? 0;

$stmt = $conn->prepare("SELECT COUNT(*) as c FROM events WHERE organizer_id=?");
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$totalEvents = $stmt->get_result()->fetch_assoc()['c'] ?? 0;

// --- Fetch Organizer's Events ---
$stmt = $conn->prepare("
  SELECT e.id, e.title, e.event_date, COUNT(t.id) as sold, IFNULL(SUM(p.amount),0) as rev
  FROM events e
  LEFT JOIN tickets t ON e.id = t.event_id
  LEFT JOIN payments p ON t.id = p.ticket_id
  WHERE e.organizer_id=?
  GROUP BY e.id, e.title, e.event_date
  ORDER BY e.event_date ASC
");
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$eventsArray = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Prepare arrays for Chart.js
$eventNames   = array_column($eventsArray, 'title');
$eventTickets = array_column($eventsArray, 'sold');
$eventRevenue = array_column($eventsArray, 'rev');

// --- EXPORT TO PDF ---
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    $fpdfPath = __DIR__ . '/fpdf/fpdf.php';
    if (!file_exists($fpdfPath)) {
        die("
        <div style='margin:50px auto;max-width:600px;text-align:center;font-family:Arial'>
          <h2 style='color:#b30000;'>❌ Missing FPDF Library</h2>
          <p>Please ensure <b>fpdf.php</b> exists at:</p>
          <code>$fpdfPath</code>
          <p>Download from <a href='http://www.fpdf.org/' target='_blank'>fpdf.org</a></p>
        </div>");
    }

    require_once($fpdfPath);

    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, 'Organizer Event Report', 0, 1, 'C');
            $this->Ln(5);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('L');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "My Events Report", 0, 1, 'C');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(80, 10, 'Event', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Date', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Tickets Sold', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Revenue (KES)', 1, 1, 'C');

    // Table body
    $pdf->SetFont('Arial', '', 10);
    foreach ($eventsArray as $row) {
        $pdf->Cell(80, 10, $row['title'], 1);
        $pdf->Cell(40, 10, $row['event_date'], 1);
        $pdf->Cell(40, 10, $row['sold'], 1, 0, 'C');
        $pdf->Cell(40, 10, number_format($row['rev'], 2), 1, 1, 'R');
    }

    // Totals
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(80, 10, 'Totals', 1);
    $pdf->Cell(40, 10, '-', 1);
    $pdf->Cell(40, 10, $totalTickets, 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($totalRevenue, 2), 1, 1, 'R');

    $pdf->Output('I', 'organizer_report.pdf');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Organizer Reports - Event Ticket System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .table-container { max-height: 500px; overflow-y: auto; }
    .floating-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #2c3e50;
      color: white;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      font-size: 24px;
      text-decoration: none;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <h2 class="mb-4">📊 My Event Reports</h2>

  <!-- Export Buttons -->
  <div class="mb-3">
    <a href="?export=pdf" class="btn btn-danger">Export My Events (PDF)</a>
  </div>

  <!-- Summary Cards -->
  <div class="row g-4">
    <div class="col-md-4"><div class="card text-center p-3"><h5>Total Revenue</h5><h2 class="text-success">KES <?= number_format($totalRevenue,2) ?></h2></div></div>
    <div class="col-md-4"><div class="card text-center p-3"><h5>Total Tickets Sold</h5><h2 class="text-primary"><?= $totalTickets ?></h2></div></div>
    <div class="col-md-4"><div class="card text-center p-3"><h5>Total Events</h5><h2 class="text-info"><?= $totalEvents ?></h2></div></div>
  </div>

  <?php if (!empty($eventsArray)): ?>
    <!-- Graphs -->
    <div class="row mt-4">
      <div class="col-md-6">
        <div class="card p-3">
          <h5 class="text-center">Tickets Sold per Event</h5>
          <canvas id="ticketsChart"></canvas>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-3">
          <h5 class="text-center">Revenue per Event (KES)</h5>
          <canvas id="revenueChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Events Table -->
    <div class="card p-3 mt-4">
      <h5>All My Events</h5>
      <div class="table-container">
        <table class="table table-striped">
          <thead><tr><th>Event</th><th>Date</th><th>Tickets Sold</th><th>Revenue (KES)</th></tr></thead>
          <tbody>
            <?php foreach ($eventsArray as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= htmlspecialchars($row['event_date']) ?></td>
              <td><?= $row['sold'] ?></td>
              <td><?= number_format($row['rev'],2) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning mt-4">⚠️ You don’t have any events yet. Create an event to see reports.</div>
  <?php endif; ?>

  <!-- Back Button -->
  <div class="mt-3">
    <a href="organizer_dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
  </div>
</div>

<!-- Floating Back Button -->
<a href="organizer_dashboard.php" class="floating-btn" title="Back to Dashboard">🏠</a>

<?php if (!empty($eventsArray)): ?>
<script>
  const eventNames = <?= json_encode($eventNames) ?>;
  const ticketsData = <?= json_encode($eventTickets) ?>;
  const revenueData = <?= json_encode($eventRevenue) ?>;

  new Chart(document.getElementById('ticketsChart'), {
    type: 'bar',
    data: {
      labels: eventNames,
      datasets: [{
        label: 'Tickets Sold',
        data: ticketsData,
        backgroundColor: 'rgba(54,162,235,0.7)',
        borderColor: 'rgba(54,162,235,1)',
        borderWidth: 1
      }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });

  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
      labels: eventNames,
      datasets: [{
        label: 'Revenue (KES)',
        data: revenueData,
        fill: true,
        backgroundColor: 'rgba(75,192,192,0.3)',
        borderColor: 'rgba(75,192,192,1)',
        tension: 0.3
      }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
</script>
<?php endif; ?>
</body>
</html>
