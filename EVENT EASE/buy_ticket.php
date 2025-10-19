<?php
// buy_ticket.php
include 'templates/header.php';
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'attendee') {
  header("Location: signin.php");
  exit;
}

if (!isset($_GET['event_id'])) {
  header("Location: browse_events.php");
  exit;
}

$event_id = intval($_GET['event_id']);
$user_id = $_SESSION['user_id'];

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
  echo "<div class='alert alert-danger'>Event not found!</div>";
  include 'templates/footer.php';
  exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $quantity = intval($_POST['quantity']);
  if ($quantity < 1) $quantity = 1;

  $total_price = $quantity * $event['price'];

  // Save to session for payment.php
  $_SESSION['event_name'] = $event['title'];
  $_SESSION['total_price'] = $total_price;

  // Redirect to payment page
  header("Location: payment.php");
  exit;
}
?>

<style>
.buy-ticket-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #74ebd5, #9face6);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 30px;
}

.buy-ticket-card {
  background: #fff;
  border-radius: 18px;
  padding: 30px;
  max-width: 500px;
  width: 100%;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  animation: fadeInUp 0.8s ease-in-out;
}

.buy-ticket-card h2 {
  font-weight: 700;
  color: #333;
  margin-bottom: 20px;
  text-align: center;
}

.buy-ticket-card p {
  font-size: 1.1rem;
  color: #555;
  text-align: center;
  margin-bottom: 25px;
}

.buy-ticket-card .form-label {
  font-weight: 600;
  color: #444;
}

.buy-ticket-card .form-control {
  border-radius: 12px;
  padding: 12px;
}

.buy-ticket-card .btn {
  border-radius: 25px;
  padding: 12px 20px;
  font-size: 1rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.buy-ticket-card .btn-secondary {
  background: linear-gradient(90deg, #43c6ac, #191654);
  border: none;
  color: #fff;
}

.buy-ticket-card .btn-secondary:hover {
  background: linear-gradient(90deg, #191654, #43c6ac);
  transform: scale(1.05);
  box-shadow: 0 6px 18px rgba(25,22,84,0.3);
}
</style>

<div class="buy-ticket-bg">
  <div class="buy-ticket-card">
    <h2>Buy Ticket for <?= htmlspecialchars($event['title']) ?></h2>
    <p><strong>Price per Ticket:</strong> KES <?= number_format($event['price'], 2) ?></p>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Number of Tickets</label>
        <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Total Price (KES)</label>
        <input type="text" id="totalPrice" class="form-control" value="<?= number_format($event['price'], 2) ?>" readonly>
      </div>

      <button type="submit" class="btn btn-secondary w-100">
        <i class="fas fa-ticket-alt"></i> Proceed to Payment
      </button>
    </form>
  </div>
</div>

<script>
  const pricePerTicket = <?= $event['price'] ?>;
  const quantityInput = document.getElementById("quantity");
  const totalPriceInput = document.getElementById("totalPrice");

  quantityInput.addEventListener("input", () => {
    const qty = parseInt(quantityInput.value) || 1;
    const total = qty * pricePerTicket;
    totalPriceInput.value = total.toFixed(2);
  });
</script>

<div class="back-btn" align="center" style="margin-top: 20px;">
  <a href="attendee_dashboard.php" class="btn btn-secondary btn-lg">
    &larr; Back to Dashboard
  </a>
</div>

<?php include 'templates/footer.php'; ?>
