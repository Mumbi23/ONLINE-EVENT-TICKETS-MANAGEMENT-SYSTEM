<?php
// payments.php — Attendee view of their payments + PDF Receipt
require_once __DIR__ . '/db.php';
session_start();

// ✅ Ensure attendee is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'attendee') {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Handle PDF receipt generation
if (isset($_GET['receipt_id'])) {
    $receiptId = intval($_GET['receipt_id']);

    // Fetch that specific payment
    $stmt = $conn->prepare("SELECT p.*, e.title AS event_name, u.username AS attendee_name 
                            FROM payments p
                            JOIN events e ON p.event_id = e.id
                            JOIN users u ON p.user_id = u.id
                            WHERE p.id=? AND p.user_id=?");
    $stmt->bind_param("ii", $receiptId, $user_id);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();

    if (!$payment) {
        die("Invalid or unauthorized receipt request.");
    }

    // ✅ Load FPDF
    require_once __DIR__ . '/fpdf/fpdf.php';

    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial','B',14);
            $this->Cell(0,10,'Event Ticketing System - Payment Receipt',0,1,'C');
            $this->Ln(5);
        }
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    // ✅ Create PDF
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',12);

    $pdf->Cell(0,10,'Receipt No: '.$payment['id'],0,1);
    $pdf->Cell(0,10,'Attendee: '.$payment['attendee_name'],0,1);
    $pdf->Cell(0,10,'Event: '.$payment['event_name'],0,1);
    $pdf->Cell(0,10,'Amount: KES '.number_format($payment['amount'],2),0,1);
    $pdf->Cell(0,10,'Payment Date: '.date('M d, Y h:i A', strtotime($payment['payment_date'])),0,1);
    $pdf->Cell(0,10,'Status: '.ucfirst($payment['status']),0,1);
    $pdf->Ln(10);
    $pdf->MultiCell(0,8,"Thank you for your payment. Please keep this receipt for your records.");

    $pdf->Output('I', 'Payment_Receipt_'.$payment['id'].'.pdf');
    exit();
}

// ✅ Fetch all payments made by this attendee
$sql = "SELECT p.id, e.title AS event_name, p.amount, p.payment_date, p.status
        FROM payments p
        JOIN events e ON p.event_id = e.id
        WHERE p.user_id = ?
        ORDER BY p.payment_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Payments</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(120deg, #f093fb, #f5576c);
  margin: 0;
  padding: 0;
  color: #333;
  min-height: 100vh;
}
.container {
  max-width: 950px;
  margin: 60px auto;
  background: #fff;
  padding: 35px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  animation: fadeIn 0.8s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
h2 {
  text-align: center;
  color: #222;
  margin-bottom: 30px;
  letter-spacing: 1px;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 14px 16px;
  border-bottom: 1px solid #eee;
  text-align: left;
}
th {
  background-color: #9b5de5;
  color: white;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
tr:nth-child(even) {
  background-color: #f8f9fa;
}
tr:hover {
  background-color: #f1f1f1;
  transform: scale(1.01);
  transition: all 0.2s ease-in-out;
}
.status {
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 12px;
  display: inline-block;
  text-align: center;
}
.status.paid {
  color: #2e7d32;
  background-color: #c8f7c5;
}
.status.pending {
  color: #b9770e;
  background-color: #ffecb3;
}
.btn {
  padding: 7px 14px;
  border-radius: 8px;
  text-decoration: none;
  color: white;
  font-weight: 500;
  transition: 0.3s;
  display: inline-block;
}
.btn-download {
  background: #6a0572;
}
.btn-download:hover {
  background: #8d1b8a;
  transform: scale(1.05);
}
.no-records {
  text-align: center;
  color: #777;
  font-style: italic;
  padding: 20px 0;
}
.back-btn {
  display: inline-block;
  margin-top: 25px;
  background: #14213d;
  color: white;
  padding: 10px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  transition: 0.3s;
}
.back-btn:hover {
  background: #fca311;
  color: #14213d;
}
.footer {
  text-align: center;
  margin-top: 40px;
  font-size: 13px;
  color: #555;
}
</style>
</head>
<body>
  <div class="container">
    <h2>💰 My Payment History</h2>
    <?php if ($result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Event</th>
            <th>Amount (KES)</th>
            <th>Date</th>
            <th>Status</th>
            <th>Receipt</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $i++; ?></td>
              <td><?= htmlspecialchars($row['event_name']); ?></td>
              <td><strong><?= number_format($row['amount'], 2); ?></strong></td>
              <td><?= date('M d, Y h:i A', strtotime($row['payment_date'])); ?></td>
              <td><span class="status <?= strtolower($row['status']); ?>"><?= ucfirst($row['status']); ?></span></td>
              <td>
                <a href="payments.php?receipt_id=<?= $row['id'] ?>" class="btn btn-download">📄 Download</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="no-records">😕 You haven’t made any payments yet.</p>
    <?php endif; ?>

    <a href="attendee_dashboard.php" class="back-btn">← Back to Dashboard</a>
    <div class="footer">Event Ticketing System &copy; <?= date('Y') ?> | Powered by <b>OAMS</b></div>
  </div>
</body>
</html>
