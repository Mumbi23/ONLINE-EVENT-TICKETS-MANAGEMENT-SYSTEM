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

// Handle confirmed deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $_SESSION['toast_message'] = "🗑️ User deleted successfully!";
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete User | Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #1e293b, #0f172a);
      color: #f9fafb;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }

    .delete-card {
      background: #ffffff;
      color: #111827;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
      padding: 2.5rem;
      text-align: center;
      max-width: 480px;
      width: 100%;
      animation: fadeInUp 0.7s ease;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .delete-card i {
      font-size: 60px;
      color: #dc2626;
      margin-bottom: 1rem;
    }

    h2 {
      color: #dc2626;
      font-weight: 700;
    }

    p {
      font-size: 1.1rem;
      color: #374151;
      margin-bottom: 2rem;
    }

    .btn-danger {
      background: linear-gradient(90deg, #dc2626, #b91c1c);
      border: none;
      font-weight: 600;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-danger:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
    }

    .btn-secondary {
      background-color: #6b7280;
      border: none;
      font-weight: 600;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-secondary:hover {
      background-color: #4b5563;
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    }

    .btn-container {
      display: flex;
      gap: 1rem;
      justify-content: center;
    }
  </style>
</head>
<body>

<div class="delete-card">
  <i class="fas fa-triangle-exclamation"></i>
  <h2>Confirm Deletion</h2>
  <p>Are you sure you want to delete this user?<br>This action cannot be undone.</p>

  <form method="post" class="btn-container">
    <button type="submit" name="confirm_delete" class="btn btn-danger px-4 py-2">
      <i class="fas fa-trash"></i> Delete
    </button>
    <a href="admin_dashboard.php" class="btn btn-secondary px-4 py-2">
      <i class="fas fa-times-circle"></i> Cancel
    </a>
  </form>
</div>

</body>
</html>
