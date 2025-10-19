<?php
require_once __DIR__ . '/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("User ID not specified");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM users WHERE id=$id");
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);
    $stmt->execute();

    // Set session flag for toast message
    $_SESSION['toast_message'] = "✅ User updated successfully!";
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User | Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #111827, #1f2937);
      font-family: 'Segoe UI', sans-serif;
      color: #fff;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .edit-card {
      background: #fff;
      color: #333;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.2);
      padding: 2rem;
      width: 100%;
      max-width: 500px;
      animation: fadeInUp 0.7s ease;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .edit-card h3 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #007bff;
      font-weight: bold;
    }

    .form-control {
      border-radius: 10px;
      border: 1.5px solid #007bff;
      padding: 10px;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #38bdf8;
      box-shadow: 0 0 8px rgba(56, 189, 248, 0.3);
    }

    .btn-primary {
      background: linear-gradient(90deg, #3b82f6, #60a5fa);
      border: none;
      font-weight: 600;
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, #2563eb, #3b82f6);
      color: #fff;
    }

    .btn-secondary {
      background-color: #6b7280;
      border: none;
      font-weight: 600;
    }

    .btn-secondary:hover {
      background-color: #4b5563;
    }

    /* Toast Notification */
    .toast-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1055;
    }
  </style>
</head>
<body>

<div class="edit-card">
  <h3><i class="fas fa-user-edit"></i> Edit User</h3>
  <form method="post">
    <div class="mb-3">
      <label class="form-label"><i class="fas fa-user"></i> Full Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label"><i class="fas fa-user-shield"></i> Role</label>
      <select name="role" class="form-control">
        <option value="attendee" <?= $user['role']=='attendee'?'selected':'' ?>>Attendee</option>
        <option value="organizer" <?= $user['role']=='organizer'?'selected':'' ?>>Organizer</option>
        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
      </select>
    </div>

    <div class="d-flex justify-content-between">
      <button type="submit" class="btn btn-primary w-50 me-2">
        <i class="fas fa-save"></i> Update
      </button>
      <a href="admin_dashboard.php" class="btn btn-secondary w-50">
        <i class="fas fa-times-circle"></i> Cancel
      </a>
    </div>
  </form>
</div>

<!-- Toast Notification (only visible on dashboard redirect) -->
<?php if (isset($_SESSION['toast_message'])): ?>
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast align-items-center text-bg-success border-0 show">
      <div class="d-flex">
        <div class="toast-body">
          <?= $_SESSION['toast_message'] ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
  <?php unset($_SESSION['toast_message']); ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
