<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: signin.php");
  exit;
}

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $subject = trim($_POST['subject']);
  $message = trim($_POST['message']);

  if ($name && $email && $subject && $message) {
    // Normally you’d send an email or save to database here.
    // For demo purposes, we’ll just show a success message.
    $success = "Your message has been sent successfully!";
  } else {
    $error = "Please fill in all fields.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Us | OETMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: linear-gradient(135deg, #2575fc, #6a11cb);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 40px 10px;
}
.contact-card {
  background: #fff;
  max-width: 700px;
  width: 100%;
  border-radius: 18px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  padding: 30px;
  animation: fadeIn 0.8s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
h2 {
  color: #2575fc;
  font-weight: 700;
}
.icon-box {
  background: linear-gradient(135deg, #2575fc, #6a11cb);
  color: #fff;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}
.icon-box i {
  font-size: 28px;
  margin-bottom: 10px;
}
.btn-primary {
  background: #2575fc;
  border: none;
  border-radius: 25px;
  padding: 10px 25px;
  font-weight: 500;
  transition: 0.3s;
}
.btn-primary:hover {
  background: #6a11cb;
}
.btn-secondary {
  background: #6c757d;
  border-radius: 25px;
  color: #fff;
}
textarea {
  resize: none;
}
</style>
</head>
<body>

<div class="contact-card">
  <h2 class="text-center mb-3"><i class="fa fa-envelope-open-text"></i> Contact Us</h2>
  <p class="text-center text-muted mb-4">We’d love to hear from you! Send us a message below.</p>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" class="mb-4">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label">Subject</label>
        <input type="text" name="subject" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label">Message</label>
        <textarea name="message" rows="4" class="form-control" placeholder="Write your message..." required></textarea>
      </div>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary w-100 mb-2"><i class="fa fa-paper-plane"></i> Send Message</button>
    </div>
  </form>

  <div class="row text-center g-3">
    <div class="col-md-4">
      <div class="icon-box">
        <i class="fa fa-phone"></i>
        <p class="mb-0">+254 740443752</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="icon-box">
        <i class="fa fa-envelope"></i>
        <p class="mb-0">support@eventease.com</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="icon-box">
        <i class="fa fa-map-marker-alt"></i>
        <p class="mb-0">Nairobi, Kenya</p>
      </div>
    </div>
  </div>
              <div class="text-center mt-4">
      <a href="attendee_dashboard.php" class="btn btn-secondary w-100"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
