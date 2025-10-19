<?php
// confirm_payment.php
session_start();
include 'db.php';
include 'templates/header.php';

// Ensure user is logged in as attendee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'attendee') {
  header("Location: signin.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Optional: Get event info from session
$event_name = $_SESSION['event_name'] ?? null;
$total_price = $_SESSION['total_price'] ?? null;

// Update latest unpaid ticket for the user as "Paid"
$stmt = $conn->prepare("
  UPDATE tickets 
  SET status = 'Paid' 
  WHERE user_id = ? 
  ORDER BY id DESC 
  LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Clear session payment data
unset($_SESSION['event_name']);
unset($_SESSION['total_price']);
?>

<style>
.confirm-bg {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #11998e, #38ef7d);
  font-family: 'Poppins', sans-serif;
  color: #fff;
}

.confirm-card {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 20px;
  padding: 40px;
  max-width: 480px;
  text-align: center;
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
  backdrop-filter: blur(10px);
}

.confirm-card h2 {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 15px;
}

.confirm-card p {
  font-size: 1.1rem;
  margin-bottom: 25px;
}

.confirm-card .btn {
  border-radius: 30px;
  font-weight: 600;
  padding: 12px 25px;
  transition: all 0.3s ease;
}

.confirm-card .btn-success {
  background: linear-gradient(90deg, #00c851, #007e33);
  border: none;
}

.confirm-card .btn-success:hover {
  transform: scale(1.08);
  box-shadow: 0 10px 25px rgba(0,200,81,0.4);
}
</style>

<div class="confirm-bg">
  <div class="confirm-card">
    <i class="fas fa-check-circle fa-4x mb-3"></i>
    <h2>Payment Confirmed!</h2>

    <?php if ($event_name && $total_price): ?>
      <p>You have successfully paid <strong>KES <?= number_format($total_price, 2) ?></strong> for 
      <strong><?= htmlspecialchars($event_name) ?></strong>.</p>
    <?php else: ?>
      <p>Your payment has been confirmed successfully.</p>
    <?php endif; ?>

    <a href="attendee_dashboard.php" class="btn btn-success">
      <i class="fas fa-home"></i> Back to Dashboard
    </a>
  </div>
</div>

<?php include 'templates/footer.php'; ?>
