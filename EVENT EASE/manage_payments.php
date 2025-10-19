<?php
// manage_payments.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit;
}

require_once __DIR__ . '/db.php';

// Fetch all payments
$payments = $conn->query("
  SELECT payments.id, users.name, payments.amount, payments.status, payments.created_at 
  FROM payments 
  JOIN users ON payments.user_id = users.id
  ORDER BY payments.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Payments | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4">💳 Manage Payments</h2>
  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>User</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while($p = $payments->fetch_assoc()): ?>
      <tr>
        <td><?= $p['id']; ?></td>
        <td><?= htmlspecialchars($p['name']); ?></td>
        <td><?= number_format($p['amount'], 2); ?></td>
        <td>
          <span class="badge bg-<?= $p['status'] === 'completed' ? 'success' : 'warning'; ?>">
            <?= ucfirst($p['status']); ?>
          </span>
        </td>
        <td><?= $p['created_at']; ?></td>
        <td>
          <a href="mark_payment.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-success">Mark Completed</a>
          <a href="delete_payment.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this payment?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  <a href="admin.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
</div>
</body>
</html>
