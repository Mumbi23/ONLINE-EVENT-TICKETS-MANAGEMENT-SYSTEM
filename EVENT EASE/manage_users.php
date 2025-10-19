<?php
// manage_users.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit;
}

require_once __DIR__ . '/db.php';

// Fetch all users
$users = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #43c6ac, #f8ffae);
      font-family: "Poppins", sans-serif;
      min-height: 100vh;
    }
    .container {
      margin-top: 70px;
      background: #ffffff;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
      animation: fadeInUp 0.8s ease;
    }
    @keyframes fadeInUp {
      from {opacity: 0; transform: translateY(30px);}
      to {opacity: 1; transform: translateY(0);}
    }
    h2 {
      color: #333;
      font-weight: 700;
      margin-bottom: 25px;
      text-align: center;
    }
    thead.table-dark {
      background: linear-gradient(90deg, #43c6ac, #f8ffae);
      color: #000;
    }
    tbody tr:hover {
      background-color: rgba(67, 198, 172, 0.1);
      transition: 0.2s ease;
    }
    .btn-warning { background-color: #ffb703; border: none; color: #fff; }
    .btn-warning:hover { background-color: #f48c06; }
    .btn-danger { background-color: #e63946; border: none; }
    .btn-danger:hover { background-color: #d90429; }
    .btn-secondary {
      background: linear-gradient(90deg, #43c6ac, #f8ffae);
      color: #000;
      border: none;
    }
  </style>
</head>
<body>
<div class="container">
  <h2><i class="fas fa-users"></i> Manage Users</h2>
  <table class="table table-striped table-bordered align-middle text-center">
    <thead class="table-dark">
      <tr>
        <th>#ID</th>
        <th><i class="fas fa-user"></i> Name</th>
        <th><i class="fas fa-envelope"></i> Email</th>
        <th><i class="fas fa-user-shield"></i> Role</th>
        <th><i class="fas fa-calendar-alt"></i> Joined</th>
        <th><i class="fas fa-cogs"></i> Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($u = $users->fetch_assoc()): ?>
      <tr>
        <td><?= $u['id']; ?></td>
        <td><?= htmlspecialchars($u['name']); ?></td>
        <td><?= htmlspecialchars($u['email']); ?></td>
        <td><?= ucfirst($u['role']); ?></td>
        <td><?= $u['created_at']; ?></td>
        <td>
          <a href="edit_user.php?id=<?= $u['id']; ?>" class="btn btn-sm btn-warning me-1">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="delete_user.php?id=<?= $u['id']; ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Are you sure you want to delete this user?');">
            <i class="fas fa-trash-alt"></i> Delete
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <div class="text-center mt-4">
    <a href="admin_dashboard.php" class="btn btn-secondary px-4 py-2">
      <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
  </div>
</div>
</body>
</html>
