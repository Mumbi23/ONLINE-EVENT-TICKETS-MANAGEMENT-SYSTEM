<?php
// admin_report.php
// Enhanced Admin Reports & Analytics Page for Event Ticket Management System with PDF Export

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security check – only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

// Include DB connection
require_once __DIR__ . '/db.php';

// ==============================
// Handle PDF Export Request
// ==============================
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    $fpdfPath = __DIR__ . '/fpdf/fpdf.php';

    // ✅ Check if FPDF file exists before requiring
    if (!file_exists($fpdfPath)) {
        die("
        <div style='margin:50px auto;max-width:600px;text-align:center;font-family:Arial'>
          <h2 style='color:#b30000;'>❌ Missing FPDF Library</h2>
          <p>Please ensure the file exists at:</p>
          <code>$fpdfPath</code>
          <br><br>
          <p>Fix:</p>
          <ul style='text-align:left;display:inline-block'>
            <li>Create a folder named <b>fpdf</b> in your project root.</li>
            <li>Download FPDF from <a href='http://www.fpdf.org/' target='_blank'>fpdf.org</a>.</li>
            <li>Place <b>fpdf.php</b> and the <b>font</b> folder inside it.</li>
          </ul>
        </div>");
    }

    require_once($fpdfPath);

    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial','B',12);
            $this->Cell(0,10,'Event Ticketing System - Admin Report',0,1,'C');
            $this->Ln(5);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(190,10,'Event Ticketing System - Admin Report',0,1,'C');
    $pdf->Ln(5);

    // Summary Data
    $totalRevenue = $conn->query("SELECT IFNULL(SUM(amount),0) AS rev FROM payments")->fetch_assoc()['rev'];
    $totalTickets = $conn->query("SELECT COUNT(*) AS c FROM tickets")->fetch_assoc()['c'];
    $totalEvents = $conn->query("SELECT COUNT(*) AS c FROM events")->fetch_assoc()['c'];

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(60,10,'Total Revenue:',0,0);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(60,10,'KES ' . number_format($totalRevenue,2),0,1);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(60,10,'Total Tickets Sold:',0,0);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(60,10,$totalTickets,0,1);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(60,10,'Total Events:',0,0);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(60,10,$totalEvents,0,1);

    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(190,10,'Event Summary',0,1,'C');
    $pdf->Ln(5);

    // Table Header
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(10,8,'#',1,0,'C');
    $pdf->Cell(60,8,'Event',1,0,'C');
    $pdf->Cell(35,8,'Date',1,0,'C');
    $pdf->Cell(35,8,'Tickets Sold',1,0,'C');
    $pdf->Cell(50,8,'Revenue (KES)',1,1,'C');

    // Table Data
    $i = 1;
    $query = "
        SELECT 
          e.id, 
          e.title, 
          e.event_date,
          COUNT(t.id) AS tickets_sold,
          IFNULL(SUM(p.amount), 0) AS revenue
        FROM events e
        LEFT JOIN tickets t ON e.id = t.event_id
        LEFT JOIN payments p ON p.ticket_id = t.id
        GROUP BY e.id
        ORDER BY e.event_date DESC
    ";
    $result = $conn->query($query);
    $pdf->SetFont('Arial','',10);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(10,8,$i++,1,0,'C');
        $pdf->Cell(60,8,substr($row['title'],0,25),1,0);
        $pdf->Cell(35,8,$row['event_date'],1,0,'C');
        $pdf->Cell(35,8,$row['tickets_sold'],1,0,'C');
        $pdf->Cell(50,8,number_format($row['revenue'],2),1,1,'C');
    }

    $pdf->Output('D','Admin_Report.pdf');
    exit();
}

// ==============================
// Dashboard View (HTML)
// ==============================

// Fetch statistics
$totalRevenue = $conn->query("SELECT IFNULL(SUM(amount),0) AS rev FROM payments")->fetch_assoc()['rev'];
$totalTickets = $conn->query("SELECT COUNT(*) AS c FROM tickets")->fetch_assoc()['c'];
$totalEvents = $conn->query("SELECT COUNT(*) AS c FROM events")->fetch_assoc()['c'];

// Monthly Revenue Data (last 6 months)
$revenueData = [];
$months = [];
$result = $conn->query("
    SELECT DATE_FORMAT(payment_date, '%M') AS month, SUM(amount) AS total
    FROM payments 
    WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY MIN(payment_date)
");
while ($row = $result->fetch_assoc()) {
    $months[] = $row['month'];
    $revenueData[] = $row['total'];
}

// Ticket Sales Per Event (top 5)
$eventNames = [];
$ticketSales = [];
$result2 = $conn->query("
    SELECT e.title, COUNT(t.id) AS sold
    FROM tickets t 
    JOIN events e ON t.event_id = e.id
    GROUP BY e.id 
    ORDER BY sold DESC 
    LIMIT 5
");
while ($row = $result2->fetch_assoc()) {
    $eventNames[] = $row['title'];
    $ticketSales[] = $row['sold'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Reports - Event Ticket System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background: linear-gradient(135deg, #d023b9ff, #7024bcff);
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      color: #222;
    }
    .container {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      padding: 30px;
      margin-top: 40px;
      margin-bottom: 50px;
    }
    h2 { font-weight: bold; color: #7024bcff; }
    .card {
      border: none; border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover { transform: translateY(-5px); box-shadow: 0 6px 18px rgba(0,0,0,0.15); }
    table thead { background-color: #7024bcff; color: #fff; }
    table tbody tr:hover { background-color: #f5f0ff; }
    .btn-secondary {
      background: linear-gradient(45deg, #7024bcff, #d023b9ff);
      border: none; color: white;
      padding: 10px 25px; border-radius: 30px;
      font-weight: 500;
    }
    .btn-secondary:hover {
      background: linear-gradient(45deg, #d023b9ff, #7024bcff);
      transform: scale(1.05); color: #fff;
    }
    .export-btn {
      background: #28a745; color: #fff;
      font-weight: 600; border-radius: 25px; padding: 8px 20px;
    }
    .export-btn:hover { background: #218838; }
  </style>
</head>
<body>
<div class="container">
  <h2 class="mb-4 text-center">📊 Reports & Analytics Dashboard</h2>
  <p class="text-center text-muted">Gain insights into your platform’s performance, revenue, and top-performing events.</p>
  <div class="text-end mb-3">
    <a href="?export=pdf" class="btn export-btn">📄 Export as PDF</a>
  </div>
  <!-- Summary Cards -->
  <div class="row g-4 mt-2">
    <div class="col-md-4">
      <div class="card text-center p-3 bg-light">
        <h5>Total Revenue</h5>
        <h2 class="text-success fw-bold">KES <?= number_format($totalRevenue,2) ?></h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center p-3 bg-light">
        <h5>Total Tickets Sold</h5>
        <h2 class="text-primary fw-bold"><?= $totalTickets ?></h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center p-3 bg-light">
        <h5>Total Events</h5>
        <h2 class="text-info fw-bold"><?= $totalEvents ?></h2>
      </div>
    </div>
  </div>
  <!-- Charts -->
  <div class="card p-4 mt-4">
    <h5 class="mb-3 text-primary">📈 Revenue (Last 6 Months)</h5>
    <canvas id="revenueChart"></canvas>
  </div>
  <div class="card p-4 mt-4">
    <h5 class="mb-3 text-primary">🎟️ Top 5 Events by Ticket Sales</h5>
    <canvas id="ticketChart"></canvas>
  </div>
  <div class="text-center mt-4">
    <a href="admin_dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('revenueChart').getContext('2d'), {
  type: 'line',
  data: {
    labels: <?= json_encode($months) ?>,
    datasets: [{
      label: 'Revenue (KES)',
      data: <?= json_encode($revenueData) ?>,
      borderColor: '#7024bcff',
      backgroundColor: 'rgba(112,36,188,0.2)',
      fill: true, tension: 0.4
    }]
  },
  options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('ticketChart').getContext('2d'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($eventNames) ?>,
    datasets: [{
      label: 'Tickets Sold',
      data: <?= json_encode($ticketSales) ?>,
      backgroundColor: ['#ff6f61', '#6a5acd', '#1abc9c', '#f39c12', '#3498db']
    }]
  },
  options: { scales: { y: { beginAtZero: true } } }
});
</script>
</body>
</html>
