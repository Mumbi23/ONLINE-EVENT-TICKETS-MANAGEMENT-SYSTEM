<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

require_once __DIR__ . '/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user profile
$stmt = $conn->prepare("SELECT name, email, profile_pic FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $profile_pic = $user['profile_pic'];
    $error = '';
    $success = '';

    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $filename = time() . "_" . basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $target_file;
        }
    }

    if (!empty($password)) {
        if ($password === $confirm_password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=?, profile_pic=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $email, $hashed, $profile_pic, $user_id);
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, profile_pic=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $profile_pic, $user_id);
    }

    if (isset($stmt) && $stmt->execute()) {
        $_SESSION['name'] = $name;
        $success = "Profile updated successfully!";
        $user['name'] = $name;
        $user['email'] = $email;
        $user['profile_pic'] = $profile_pic;
    } elseif (empty($error)) {
        $error = "Error updating profile.";
    }
    if (isset($stmt)) $stmt->close();
}

// Determine dashboard based on user role
$dashboard = 'attendee_dashboard.php'; // default
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'organizer':
            $dashboard = 'organizer_dashboard.php';
            break;
        case 'admin':
            $dashboard = 'admin_dashboard.php';
            break;
        case 'student':
            $dashboard = 'student_dashboard.php';
            break;
        case 'driver':
            $dashboard = 'driver_dashboard.php';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile | OETMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #6a11cb, #2575fc);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 10px;
    overflow-y: auto;
}
.profile-card {
    background: #fff;
    max-width: 430px;
    width: 100%;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    padding: 25px 25px 30px;
    animation: fadeInUp 0.8s ease;
}
.profile-card:hover {
    box-shadow: 0 15px 35px rgba(0,0,0,0.25);
}
.profile-pic {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 4px solid #2575fc;
    object-fit: cover;
    margin-bottom: 10px;
    transition: transform 0.3s ease;
}
.profile-pic:hover {
    transform: scale(1.05);
}
.section-title {
    font-weight: 600;
    color: #2575fc;
    margin-top: 15px;
    margin-bottom: 8px;
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
.password-field {
    position: relative;
}
.password-field .toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
}
.alert {
    text-align: left;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

<div class="profile-card text-center">
    <img src="<?= !empty($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : 'default_image.png' ?>" class="profile-pic" alt="Profile Picture">
    <h4 class="fw-bold"><?= htmlspecialchars($user['name']) ?></h4>
    <p class="text-muted mb-3"><?= htmlspecialchars($user['email']) ?></p>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?= $success; ?><br>Redirecting to your dashboard...</div>
      <script>
        setTimeout(() => { window.location.href = "<?= $dashboard ?>"; }, 2000);
      </script>
    <?php elseif (!empty($error)): ?>
      <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="text-start">
        <h6 class="section-title"><i class="fa fa-user"></i> Personal Information</h6>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" name="profile_pic" class="form-control" accept="image/*" onchange="previewImage(event)">
        </div>

        <h6 class="section-title"><i class="fa fa-lock"></i> Change Password</h6>
        <div class="mb-3 password-field">
            <label class="form-label">New Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current">
            <i class="fa fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
        </div>

        <div class="mb-4 password-field">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control">
            <i class="fa fa-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary w-100 mb-2"><i class="fa fa-save"></i> Save Changes</button>
            <a href="<?= $dashboard ?>" class="btn btn-secondary w-100"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        document.querySelector('.profile-pic').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
function togglePassword(id, el) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
        el.classList.remove("fa-eye");
        el.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        el.classList.remove("fa-eye-slash");
        el.classList.add("fa-eye");
    }
}
</script>

</body>
</html>
