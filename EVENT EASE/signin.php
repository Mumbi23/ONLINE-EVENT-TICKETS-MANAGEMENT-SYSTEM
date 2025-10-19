<?php
session_start();
require_once __DIR__ . '/db.php'; // ✅ FIX: include database connection

// signin.php - Handles user login and session creation
if (isset($_SESSION['role'])) {
    // User already logged in, redirect based on role
    echo "<script>
        alert('Session still active: " . $_SESSION['role'] . "');
    </script>";
}

// On form submit, check credentials
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Fetch user by email using prepared statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role IN ('attendee','organizer','admin') LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify password and set session variables
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if ($_SESSION['role'] === 'admin') {
            header("Location: admin_dashboard.php");
            exit;
        }

        // Redirect to the appropriate dashboard based on role
        header("Location: " . $user['role'] . "_dashboard.php");
        exit;
    } else {
        $message = "Invalid credentials!";
    }
}
?>
<?php include 'templates/header.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}

.signin-bg {
  background: 
    linear-gradient(rgba(67, 198, 172, 0.6), rgba(248, 255, 174, 0.6)),
    url("assets/images/loginback.png");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;

  min-height: 100vh;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}


.card-signin {
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(10px);
  animation: fadeInUp 1s;
}

.card-signin h2 {
  color: #43c6ac;
  font-weight: 700;
  letter-spacing: 1px;
}
.btn-success {
  background: linear-gradient(90deg, #43c6ac 60%, #f8ffae 100%);
  border: none;
  color: #222;
  font-weight: 600;
}
.btn-success:hover {
  background: linear-gradient(90deg, #43c6ac 80%, #f8ffae 100%);
  color: #007bff;
}
.btn-link {
  color: #43c6ac !important;
  font-weight: 600;
}
.form-label i {
  margin-right: 6px;
  color: #43c6ac;
}
input[type="email"], input[type="password"] {
  border-radius: 10px !important;
  border: 1.5px solid #43c6ac;
}
.alert-danger {
  background: linear-gradient(90deg, #ffdde1 0%, #ee9ca7 100%);
  color: #b71c1c;
  border: none;
  font-weight: 600;
}
</style>

<div class="signin-bg d-flex justify-content-center align-items-center">
  <div class="col-md-5">
    <div class="card card-signin border-0 animate__animated animate__fadeInUp">
      <div class="card-body p-4">
        <h2 class="fw-bold text-center mb-4">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </h2>

        <?php if (!empty($message)): ?>
          <div class="alert alert-danger text-center"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
          <div class="mb-3">
            <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><i class="fas fa-lock"></i> Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
          </div>
          <button type="submit" class="btn btn-success w-100 animate__animated animate__pulse animate__infinite">
            <i class="fas fa-sign-in-alt"></i> Sign In
          </button>
        </form>

        <div class="text-center mt-3">
          <p class="mb-0">Don't have an account?</p>
          <a href="signup.php" class="btn btn-link text-decoration-none animate__animated animate__fadeIn animate__delay-1s">
            <i class="fas fa-user-plus"></i> Sign Up
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>
