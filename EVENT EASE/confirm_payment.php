<?php
// confirm_payment.php - Confirmation page after M-Pesa payment
include 'templates/header.php';
?>

<div class="container mt-5">
  <div class="card shadow-sm mx-auto" style="max-width: 400px;">
    <div class="card-body text-center">
      <h3 class="mb-3 text-success"><i class="fas fa-check-circle"></i> Payment Confirmation</h3>
      <p class="lead">Thank you for your payment!</p>
      <p>Please enter your M-Pesa transaction code below to confirm your ticket purchase.</p>
      <form method="POST" action="attendee_dashboard.php">
        <div class="mb-3">
          <input type="text" name="mpesa_code" class="form-control" placeholder="M-Pesa Transaction Code" required>
        </div>
        <button type="submit" class="btn btn-primary">Confirm</button>
      </form>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>