<?php
// signup.php - Handles user registration (attendee or organizer)
include 'db.php';
$message = "";

// If form submitted, validate input and insert new user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $role = $_POST['role'] ?? 'attendee'; // Default to attendee if not set

  // Check if passwords match
  if ($password !== $confirm_password) {
    $message = "❌ Passwords do not match!";
  } else {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'attendee'; // Force attendee
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
      header("Location: signin.php");
      exit;
    } else {
      $message = "Error: " . $stmt->error;
    }
  }
}
?>
<?php include 'templates/header.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
.signup-bg {
  background: linear-gradient(135deg, #f8ffae 0%, #43c6ac 100%);
  min-height: 100vh;
  padding-top: 60px;
}
.card-signup {
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(67,198,172,0.15);
  background: #fff;
  animation: fadeInUp 1s;
}
.card-signup h2 {
  color: #3498db;
  font-weight: 700;
  letter-spacing: 1px;
}
.btn-primary {
  background: linear-gradient(90deg, #43c6ac 60%, #3498db 100%);
  border: none;
  color: #fff;
  font-weight: 600;
  border-radius: 20px;
}
.btn-primary:hover {
  background: linear-gradient(90deg, #3498db 80%, #43c6ac 100%);
  color: #f8ffae;
}
.btn-link {
  color: #43c6ac !important;
  font-weight: 600;
}
.form-label i {
  margin-right: 6px;
  color: #3498db;
}
input[type="text"], input[type="email"], input[type="password"], select {
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

<div class="signup-bg d-flex justify-content-center align-items-center">
  <div class="col-md-6">
    <div class="card card-signup border-0 animate__animated animate__fadeInUp">
      <div class="card-body p-4">
        <h2 class="fw-bold text-center mb-4">
          <i class="fas fa-user-plus"></i> Sign Up
        </h2>

        <?php if ($message): ?>
          <div class="alert alert-danger animate__animated animate__shakeX">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($message) ?>
          </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
          <div class="mb-3">
            <label class="form-label"><i class="fas fa-user"></i> Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><i class="fas fa-lock"></i> Password</label>
            <input type="password" name="password" class="form-control" placeholder="Create a password" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><i class="fas fa-lock"></i> Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter your password" required>
          </div>
          <div class="mb-3">
          <button type="submit" class="btn btn-primary w-100 animate__animated animate__pulse animate__infinite">
            <i class="fas fa-user-plus"></i> Sign Up
          </button>
        </form>

        <div class="text-center mt-3">
          <p class="mb-0">Already have an account?</p>
          <a href="signin.php" class="btn btn-link text-decoration-none animate__animated animate__fadeIn animate__delay-1s">
            <i class="fas fa-sign-in-alt"></i> Sign In
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>