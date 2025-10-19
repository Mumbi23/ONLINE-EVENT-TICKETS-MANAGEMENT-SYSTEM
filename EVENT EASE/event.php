<?php
// event.php - Shows detailed event information and buy option for attendees
include 'templates/header.php';
include 'db.php';

if (!isset($_GET['id'])) {
  header("Location: browse_events.php");
  exit;
}

$id = intval($_GET['id']);

// Fetch event details securely
$stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
  echo "<div class='alert alert-danger text-center my-4'><i class='fas fa-exclamation-circle'></i> Event not found!</div>";
  include 'templates/footer.php';
  exit;
}

$locationEncoded = urlencode($event['location']);
$imagePath = !empty($event['image']) && file_exists($event['image']) ? htmlspecialchars($event['image']) : 'assets/images/default_event.png';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
body { font-family: 'Poppins', sans-serif; background: #f0f2f5; padding: 30px 10px; }
.card-event-detail { border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden; animation: fadeInUp 0.8s ease; }
.card-event-detail img { width: 100%; height: 300px; object-fit: cover; }
.card-body h2 { color: #43c6ac; font-weight: 600; }
.card-body p { font-size: 1rem; margin-bottom: 10px; }
.btn-success { background: linear-gradient(90deg, #43c6ac 60%, #f8ffae 100%); color: #222; font-weight:600; border-radius: 20px; transition: 0.3s; }
.btn-success:hover { background: linear-gradient(90deg, #11998e 60%, #38ef7d 100%); color: #fff; transform: scale(1.05); }
.btn-outline-secondary { border-radius: 20px; transition: 0.3s; }
.btn-outline-secondary:hover { background: #6c757d; color: #fff; transform: scale(1.05); }
.map-frame { border: none; border-radius: 12px; width: 100%; height: 300px; margin-top: 15px; transition: transform 0.3s; }
.map-frame:hover { transform: scale(1.02); }
@keyframes fadeInUp { from { opacity:0; transform: translateY(30px);} to { opacity:1; transform:translateY(0);} }
</style>

<div class="container my-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card card-event-detail shadow-sm border-0 animate__animated animate__fadeInUp">
        <img src="<?= $imagePath ?>" alt="Event Image">
        <div class="card-body">
          <h2 class="fw-bold mb-3"><i class="fas fa-calendar-check"></i> <?= htmlspecialchars($event['title']) ?></h2>

          <p><i class="far fa-calendar-alt text-danger"></i> <strong>Date:</strong> <?= date("F j, Y", strtotime($event['event_date'])) ?></p>
          <p><i class="fas fa-map-marker-alt text-success"></i> <strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
          <p><i class="fas fa-align-left text-secondary"></i> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
          <p><i class="fas fa-money-bill-wave text-warning"></i> <strong>Price:</strong> KES <?= number_format($event['price'],2) ?></p>

          <!-- Interactive Map -->
          <iframe class="map-frame" src="https://maps.google.com/maps?q=<?= $locationEncoded ?>&t=&z=15&ie=UTF8&iwloc=&output=embed" allowfullscreen="" loading="lazy"></iframe>

          <div class="d-flex justify-content-between mt-4">
            <a href="browse_events.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to Events</a>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'attendee'): ?>
              <a href="buy_ticket.php?event_id=<?= $event['id'] ?>" class="btn btn-success"><i class="fas fa-ticket-alt"></i> Buy Ticket</a>
            <?php else: ?>
              <p class="text-muted"><i class="fas fa-info-circle"></i> Login as an Attendee to buy tickets.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>
