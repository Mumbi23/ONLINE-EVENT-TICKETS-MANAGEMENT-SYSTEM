<?php
// settings.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

require_once __DIR__ . '/db.php';
$adminId = $_SESSION['user_id'] ?? null;
$message = "";
$alertType = "info";

// Fetch admin details
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id=? AND role='admin' LIMIT 1");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc() ?: ['name' => '', 'email' => ''];
$stmt->close();

// Update Profile
if (isset($_POST['update_profile'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    if ($name && $email) {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=? AND role='admin'");
        $stmt->bind_param("ssi", $name, $email, $adminId);
        $stmt->execute();
        $stmt->close();
        $message = "✅ Profile updated successfully!";
        $alertType = "success";
    } else {
        $message = "⚠️ Name and Email cannot be empty!";
        $alertType = "warning";
    }
}

// Change Password
if (isset($_POST['change_password'])) {
    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $hash = $stmt->get_result()->fetch_assoc()['password'] ?? '';
    $stmt->close();

    if (!password_verify($_POST['current_password'], $hash)) {
        $message = "❌ Incorrect current password!";
        $alertType = "danger";
    } elseif ($_POST['new_password'] !== $_POST['confirm_password']) {
        $message = "⚠️ Passwords do not match!";
        $alertType = "warning";
    } else {
        $newHash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $newHash, $adminId);
        $stmt->execute();
        $stmt->close();
        $message = "✅ Password updated successfully!";
        $alertType = "success";
    }
}

// Edit User
if (isset($_POST['save_user_edit'])) {
    $uid = intval($_POST['edit_user_id']);
    $uname = trim($_POST['edit_name']);
    $uemail = trim($_POST['edit_email']);
    $urole = trim($_POST['edit_role']);

    if ($uname && $uemail && $urole) {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $uname, $uemail, $urole, $uid);
        $stmt->execute();
        $stmt->close();
        $message = "✅ User updated successfully!";
        $alertType = "success";
    }
}

// Delete User
if (isset($_POST['delete_user_confirm'])) {
    $deleteId = intval($_POST['delete_user_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
    $message = "🗑️ User deleted successfully!";
    $alertType = "danger";
}

// Fetch users & logs
$users = $conn->query("SELECT id, name, email, role FROM users ORDER BY id DESC");
$audit_logs = $conn->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Settings - Event Ticket System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background: radial-gradient(circle at top left, #1a1a2e, #16213e);
      font-family: 'Poppins', sans-serif;
      color: #fff;
      min-height: 100vh;
      padding: 30px;
    }
    .container {
      background: rgba(255,255,255,0.08);
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 0 25px rgba(0,0,0,0.4);
      animation: fadeIn 0.6s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
    h2 {
      text-align: center;
      font-weight: 700;
      color: #00d4ff;
      margin-bottom: 30px;
    }
    .settings-card {
      background: rgba(255,255,255,0.12);
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 25px;
      transition: 0.3s;
    }
    .settings-card:hover {
      box-shadow: 0 0 15px rgba(0,212,255,0.4);
    }
    .nav-tabs .nav-link {
      color: #ccc;
      border: none;
      border-bottom: 3px solid transparent;
    }
    .nav-tabs .nav-link.active {
      color: #00d4ff;
      border-bottom: 3px solid #00d4ff;
      font-weight: 600;
    }
    .form-control, .form-select {
      background: rgba(255,255,255,0.15);
      color: #fff;
      border: 1px solid rgba(255,255,255,0.2);
    }
    .form-control:focus, .form-select:focus {
      border-color: #00d4ff;
      background: rgba(255,255,255,0.2);
      box-shadow: none;
    }
    .btn-primary { background: #00b4d8; border: none; }
    .btn-primary:hover { background: #0096c7; }
    .btn-warning { background: #ffb703; border: none; color: #fff; }
    .btn-warning:hover { background: #fb8500; }
    .btn-danger { background: #e63946; border: none; }
    .btn-danger:hover { background: #b71c1c; }
    table { color: #fff; }
    .table-striped>tbody>tr:nth-of-type(odd)>* {
      background-color: rgba(255,255,255,0.05);
    }
    .modal-content {
      background: #1b1b2f;
      color: #fff;
      border-radius: 10px;
      border: none;
    }
    .alert {
      background: rgba(0,0,0,0.5);
      border: none;
      color: #fff;
      border-left: 5px solid #00d4ff;
    }
    .alert-success { border-left-color: #2ecc71; }
    .alert-danger { border-left-color: #e63946; }
    .alert-warning { border-left-color: #ffb703; }
    footer {
      text-align: center;
      margin-top: 30px;
      color: #aaa;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<div class="container">
  <h2><i class="fas fa-cogs"></i> Admin Settings</h2>

  <?php if ($message): ?>
    <div class="alert alert-<?= $alertType ?>"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#profile">Profile</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#users">Manage Users</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#security">Security & Maintenance</a></li>
  </ul>

  <div class="tab-content">

    <!-- Profile -->
    <div class="tab-pane fade show active" id="profile">
      <div class="settings-card">
        <form method="POST">
          <input type="hidden" name="update_profile">
          <div class="mb-3"><label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($admin['name']) ?>" required></div>
          <div class="mb-3"><label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required></div>
          <button type="submit" class="btn btn-primary w-100">Save Changes</button>
        </form>

        <hr class="text-light">
        <h5>Change Password</h5>
        <form method="POST">
          <input type="hidden" name="change_password">
          <div class="mb-3"><label>Current Password</label><input type="password" name="current_password" class="form-control" required></div>
          <div class="mb-3"><label>New Password</label><input type="password" name="new_password" class="form-control" required></div>
          <div class="mb-3"><label>Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
          <button type="submit" class="btn btn-warning w-100">Update Password</button>
        </form>
      </div>
    </div>

    <!-- Manage Users -->
    <div class="tab-pane fade" id="users">
      <div class="settings-card">
        <h5>👥 Manage Users</h5>
        <table class="table table-striped table-hover mt-3 text-center">
          <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
          <tbody>
            <?php while ($u = $users->fetch_assoc()): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td>
                  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                    data-id="<?= $u['id'] ?>" data-name="<?= htmlspecialchars($u['name']) ?>" data-email="<?= htmlspecialchars($u['email']) ?>" data-role="<?= $u['role'] ?>">Edit</button>
                  <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal" 
                    data-id="<?= $u['id'] ?>" data-name="<?= htmlspecialchars($u['name']) ?>">Delete</button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Security -->
    <div class="tab-pane fade" id="security">
      <div class="settings-card">
        <h5>🔒 System Security & Maintenance</h5>
        <form method="POST" class="mb-3">
          <button name="run_backup" class="btn btn-success w-100">Run Database Backup</button>
        </form>
        <hr class="text-light">
        <h6>🧾 Recent Audit Logs</h6>
        <ul class="list-group mt-2">
          <?php while ($log = $audit_logs->fetch_assoc()): ?>
            <li class="list-group-item bg-transparent text-light border-light">
              <strong><?= htmlspecialchars($log['user']) ?>:</strong> <?= htmlspecialchars($log['action']) ?><br>
              <small class="text-muted"><?= $log['created_at'] ?></small>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>
    </div>
  </div>

  <a href="admin_dashboard.php" class="btn btn-secondary w-100 mt-3"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST">
      <div class="modal-header bg-primary text-white"><h5>Edit User</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="save_user_edit" value="1">
        <input type="hidden" name="edit_user_id" id="edit_user_id">
        <div class="mb-3"><label>Name</label><input type="text" name="edit_name" id="edit_name" class="form-control"></div>
        <div class="mb-3"><label>Email</label><input type="email" name="edit_email" id="edit_email" class="form-control"></div>
        <div class="mb-3"><label>Role</label>
          <select name="edit_role" id="edit_role" class="form-select">
            <option value="attendee">Attendee</option>
            <option value="organizer">Organizer</option>
            <option value="admin">Admin</option>
          </select></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST">
      <div class="modal-header bg-danger text-white"><h5>Confirm Delete</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="delete_user_confirm" value="1">
        <input type="hidden" name="delete_user_id" id="delete_user_id">
        <p>Are you sure you want to delete <strong id="delete_user_name"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger">Delete</button>
      </div>
    </form>
  </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget;
  if (e.target.id === 'editUserModal') {
    edit_user_id.value = btn.dataset.id;
    edit_name.value = btn.dataset.name;
    edit_email.value = btn.dataset.email;
    edit_role.value = btn.dataset.role;
  } else if (e.target.id === 'deleteUserModal') {
    delete_user_id.value = btn.dataset.id;
    delete_user_name.textContent = btn.dataset.name;
  }
});
</script>

<footer>
  <p>&copy; <?= date('Y'); ?> Event Ticketing Admin Panel. All rights reserved.</p>
</footer>
</body>
</html>
