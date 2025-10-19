<?php
// edit_event.php
session_start();
require_once __DIR__ . '/db.php';

// Only logged-in users (organizer or admin) can access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['organizer', 'admin'])) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Admins can edit all events, organizers only their own
if ($role === 'admin') {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
} else {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND organizer_id = ?");
    $stmt->bind_param("ii", $event_id, $user_id);
}

$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    die("<h3 class='text-center text-danger mt-5'>❌ Event not found or permission denied.</h3>");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $date = trim($_POST['event_date']);
    $image_path = $event['image'];

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    // Update query (admins don’t need organizer_id restriction)
    if ($role === 'admin') {
        $update = $conn->prepare("UPDATE events 
                                  SET title=?, description=?, location=?, event_date=?, image=?
                                  WHERE id=?");
        $update->bind_param("sssssi", $title, $description, $location, $date, $image_path, $event_id);
    } else {
        $update = $conn->prepare("UPDATE events 
                                  SET title=?, description=?, location=?, event_date=?, image=?
                                  WHERE id=? AND organizer_id=?");
        $update->bind_param("ssssssi", $title, $description, $location, $date, $image_path, $event_id, $user_id);
    }

    if ($update->execute()) {
        $_SESSION['message'] = "✅ Event updated successfully!";
        header("Location: " . ($role === 'admin' ? "admin_dashboard.php" : "organizer_dashboard.php"));
        exit;
    } else {
        echo "<p class='text-danger text-center mt-3'>❌ Error updating event.</p>";
    }
    $update->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Event | EventEase</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #43c6ac, #f8ffae);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 25px;
}

/* Edit card styling */
.edit-card {
    background: #fff;
    border-radius: 25px;
    padding: 35px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    max-width: 700px;
    width: 100%;
    animation: fadeInUp 0.9s ease;
    position: relative;
    overflow: hidden;
}
.edit-card::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: linear-gradient(135deg, rgba(67,198,172,0.05), rgba(248,255,174,0.05));
    border-radius: 25px;
    pointer-events: none;
}
h3 {
    text-align: center;
    color: #43c6ac;
    font-weight: 700;
    margin-bottom: 30px;
}

/* Inputs */
.form-label { font-weight: 600; color: #444; }
.form-control {
    border-radius: 12px;
    padding: 12px;
    border: 1px solid #ccc;
    transition: 0.3s;
}
.form-control:focus {
    border-color: #43c6ac;
    box-shadow: 0 0 10px rgba(67,198,172,0.4);
}

/* Buttons */
.btn-gradient {
    background: linear-gradient(90deg, #43c6ac, #f8ffae);
    color: #000;
    font-weight: 600;
    border: none;
    border-radius: 50px;
    padding: 12px 30px;
    transition: 0.3s;
}
.btn-gradient:hover {
    background: linear-gradient(90deg, #11998e, #38ef7d);
    color: #fff;
    transform: scale(1.03);
}
.btn-secondary {
    border-radius: 50px;
    background: #6c757d;
    color: #fff;
}
.btn-secondary:hover {
    background: #5a6268;
}

/* Image Preview */
img.img-fluid {
    border-radius: 12px;
    border: 1px solid #ddd;
    margin-top: 10px;
    max-height: 180px;
    transition: transform 0.3s, box-shadow 0.3s;
}
img.img-fluid:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}

/* Animation */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
<div class="edit-card">
  <h3><i class="fa fa-edit"></i> Edit Event</h3>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Event Title</label>
      <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($event['description']); ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Location</label>
      <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($event['location']); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Event Date</label>
      <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($event['event_date']); ?>" required>
    </div>
    <div class="mb-4">
      <label class="form-label">Event Image</label>
      <input type="file" name="image" class="form-control" accept="image/*">
      <?php if (!empty($event['image'])): ?>
        <img src="<?= htmlspecialchars($event['image']); ?>" alt="Event Image" class="img-fluid mt-2">
      <?php endif; ?>
    </div>
    <div class="d-flex justify-content-between">
      <a href="<?= $role === 'admin' ? 'admin_dashboard.php' : 'organizer_dashboard.php' ?>" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back
      </a>
      <button type="submit" class="btn btn-gradient">
        <i class="fa fa-save"></i> Update Event
      </button>
    </div>
  </form>
</div>
</body>
</html>
