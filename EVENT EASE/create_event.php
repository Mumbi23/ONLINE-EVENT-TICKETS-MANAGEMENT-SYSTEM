<?php
// create_event.php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: signin.php");
    exit;
}

require_once __DIR__ . '/db.php';

$organizer_id = $_SESSION['user_id'];
$message = "";

// Handle event creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $date = trim($_POST['event_date']);
    $location = trim($_POST['location']);
    $price = floatval($_POST['price']);
    $image_path = null;

    // 🔒 Backend validation: prevent past dates
    $today = date('Y-m-d');
    if ($date < $today) {
        $message = "<div class='alert alert-danger'>❌ You cannot select a past date for your event.</div>";
    } else {
        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir);
            $filename = time() . "_" . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $filename;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            }
        }

        if ($title && $date && $location) {
            $stmt = $conn->prepare("INSERT INTO events (title, event_date, location, price, organizer_id, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdis", $title, $date, $location, $price, $organizer_id, $image_path);

            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>✅ Event created successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>❌ Failed to create event. Please try again.</div>";
            }
        } else {
            $message = "<div class='alert alert-warning'>⚠ Please fill in all required fields.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Event | OETMS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* Body */
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 10px;
}

/* Card container */
.card {
    background: #ffffff10;
    border-radius: 20px;
    padding: 40px 30px;
    backdrop-filter: blur(15px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    width: 100%;
    max-width: 550px;
    color: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.4);
}

/* Form title */
.card h3 {
    font-weight: 600;
    text-align: center;
    margin-bottom: 30px;
    color: #ffd700;
}

/* Form controls */
.form-control {
    border-radius: 12px;
    border: none;
    padding: 12px 15px;
    background: rgba(255,255,255,0.15);
    color: #fff;
    margin-bottom: 15px;
    transition: background 0.3s, box-shadow 0.3s;
}
.form-control:focus {
    background: rgba(255,255,255,0.25);
    outline: none;
    box-shadow: 0 0 8px #ffd700;
    color: #fff;
}

/* Labels */
.form-label {
    font-weight: 500;
    margin-bottom: 5px;
    display: block;
}

/* Buttons */
.btn {
    border-radius: 50px;
    font-weight: 600;
    padding: 10px 25px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 215, 0, 0.5);
}

.btn-success {
    background: #ffd700;
    color: #1e3c72;
    border: none;
}
.btn-success:hover {
    background: #ffea00;
    color: #1e3c72;
}

.btn-light {
    background: rgba(255,255,255,0.2);
    color: #fff;
    border: none;
}
.btn-light:hover {
    background: rgba(255,255,255,0.35);
}

/* Alert messages */
.alert {
    border-radius: 12px;
    padding: 12px 15px;
    font-weight: 500;
    margin-bottom: 15px;
}

/* Uploaded image preview */
img.img-fluid {
    border-radius: 10px;
    border: 1px solid #ddd;
    margin-top: 10px;
    max-height: 150px;
}
</style>
</head>
<body>

<div class="card">
  <h3><i class="fa-solid fa-calendar-plus"></i> Create New Event</h3>
  <?= $message; ?>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Event Title</label>
      <input type="text" name="title" class="form-control" placeholder="Enter event title" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Event Date</label>
      <input type="date" name="event_date" id="event_date" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Location</label>
      <input type="text" name="location" class="form-control" placeholder="Enter event location" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Ticket Price (KSH)</label>
      <input type="number" step="0.01" name="price" class="form-control" placeholder="Enter ticket price">
    </div>
    <div class="mb-3">
      <label class="form-label">Event Image</label>
      <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <button type="submit" class="btn btn-success"><i class="fa-solid fa-plus"></i> Create Event</button>
    <a href="event_manage.php" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Back</a>
  </form>
</div>

<!-- JS to prevent selecting past dates -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('event_date').setAttribute('min', today);
});
</script>

</body>
</html>
