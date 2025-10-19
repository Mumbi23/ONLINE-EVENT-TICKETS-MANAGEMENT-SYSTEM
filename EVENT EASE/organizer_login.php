<?php
// organizer_login.php
session_start();
require_once "db.php"; // adjust if your db.php is elsewhere

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check users table for an organizer role
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? AND role = 'organizer'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify hashed password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = 'organizer';  // important for role-based access
            header("Location: organizer_dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Organizer Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #2c3e50, #3498db);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-box {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 400px;
    }
    h3 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c3e50;
    }
    .btn-primary {
      background-color: #3498db;
      border: none;
    }
    .btn-primary:hover {
      background-color: #2980b9;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h3>Organizer Login</h3>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>
</body>
</html>
