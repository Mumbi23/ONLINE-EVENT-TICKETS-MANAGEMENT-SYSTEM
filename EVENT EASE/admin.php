<?php
// admin.php
// Admin Login Page

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php'; // include DB connection

$error = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Fetch user with role=admin
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verify password
            if (password_verify($password, $row['password'])) {
                // Success → set session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = "admin";

                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "No admin found with that email!";
        }
    } else {
        $error = "Please enter both email and password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - Event Ticket System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-card {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
    .btn-primary {
      background-color: #3498db;
      border: none;
    }
    .btn-primary:hover {
      background-color: #2980b9;
    }
    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h3 class="text-center mb-4">🔑 Admin Login</h3>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
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
