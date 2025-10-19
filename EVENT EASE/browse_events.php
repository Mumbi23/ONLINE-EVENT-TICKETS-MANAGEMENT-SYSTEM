<?php
include 'templates/header.php';
include 'db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Determine dashboard link based on role
$dashboardLink = 'signin.php';
if (!empty($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') $dashboardLink = 'admin_dashboard.php';
    elseif ($_SESSION['role'] === 'organizer') $dashboardLink = 'organizer_dashboard.php';
    else $dashboardLink = 'attendee_dashboard.php';
}

// Retrieve events ordered by date
$result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
/* Background */
.browse-events-bg {
  background: linear-gradient(135deg, #f8ffae 0%, #43c6ac 100%);
  min-height: 100vh;
  padding: 30px 20px 60px;
  font-family: 'Poppins', sans-serif;
}

/* Event Card */
.card-event {
  border-radius: 18px;
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  position: relative;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}
.card-event:hover {
  transform: translateY(-10px) scale(1.05);
  box-shadow: 0 16px 48px rgba(0,0,0,0.25);
}

/* Image */
.card-img-top {
  height: 200px;
  object-fit: cover;
  border-radius: 18px 18px 0 0;
  transition: transform 0.3s;
}
.card-event:hover .card-img-top {
  transform: scale(1.1);
}

/* Gradient overlay on image */
.card-event::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 200px;
  background: linear-gradient(180deg, rgba(0,0,0,0.2), rgba(0,0,0,0.5));
  border-radius: 18px 18px 0 0;
  pointer-events: none;
}

/* Card content */
.card-body {
  display: flex;
  flex-direction: column;
  padding: 20px;
}
.card-title i {
  margin-right: 8px;
  color: #ff6b6b;
}
.card-text {
  font-size: 0.95rem;
  color: #555;
}

/* Event date & location */
.card p i {
  margin-right: 6px;
}

/* Buttons */
.btn-info {
  background: linear-gradient(90deg, #43c6ac 60%, #f8ffae 100%);
  color: #222;
  font-weight: 600;
  border-radius: 20px;
  border: none;
  transition: 0.3s;
}
.btn-info:hover {
  background: linear-gradient(90deg, #3498db 60%, #43c6ac 100%);
  color: #fff;
  transform: scale(1.05);
}
.btn-secondary {
  background-color: #6c757d;
  color: #fff;
  font-weight: 500;
  border-radius: 20px;
  padding: 10px 25px;
  transition: background 0.3s, transform 0.2s;
  text-decoration: none;
}
.btn-secondary:hover {
  background-color: #5a6268;
  transform: scale(1.05);
  color: #fff;
}

/* Animated badge */
.badge-upcoming {
  position: absolute;
  top: 15px;
  left: 15px;
  background: #ff4757;
  color: #fff;
  padding: 6px 12px;
  font-size: 0.8rem;
  border-radius: 12px;
  font-weight: 600;
  animation: pulse 1.5s infinite;
}
@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

/* Container */
.container.mx-auto {
  max-width: 1200px;
}
</style>

<div class="browse-events-bg">
  <div class="text-center my-4 animate__animated animate__fadeInDown">
    <h2 class="fw-bold">
      <i class="fas fa-calendar-alt text-primary"></i> Browse Upcoming Events
    </h2>
    <p class="lead text-muted">Find your next experience and book your tickets today.</p>
  </div>

  <div class="row g-4 container mx-auto">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 position-relative">
          <div class="card card-event shadow-sm animate__animated animate__fadeInUp">
            <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
              <img src="<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="Event Image">
            <?php else: ?>
              <img src="assets/images/default_event.png" class="card-img-top" alt="Event Image">
            <?php endif; ?>

            <div class="card-body d-flex flex-column">
              <h5 class="card-title">
                <i class="fas fa-microphone-alt"></i> <?= htmlspecialchars($row['title']) ?>
              </h5>
              <p class="card-text">
                <?= substr(htmlspecialchars($row['description']), 0, 120) ?>...
              </p>
              <p class="mb-1">
                <i class="far fa-calendar text-danger"></i>
                <strong><?= date("F j, Y", strtotime($row['event_date'])) ?></strong>
              </p>
              <p class="mb-3">
                <i class="fas fa-map-marker-alt text-success"></i>
                <?= htmlspecialchars($row['location']) ?>
              </p>

              <div class="mt-auto">
                <a href="event.php?id=<?= $row['id'] ?>" class="btn btn-info w-100 animate__animated animate__pulse animate__infinite">
                  <i class="fas fa-eye"></i> View Details
                </a>
              </div>
            </div>

            <!-- Upcoming Badge -->
            <?php if (strtotime($row['event_date']) > time()): ?>
              <div class="badge-upcoming">Upcoming</div>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12 text-center animate__animated animate__fadeIn">
        <p class="text-muted"><i class="fas fa-info-circle"></i> No upcoming events found.</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Back to Dashboard -->
  <div class="text-center my-4">
    <a href="<?= $dashboardLink ?>" class="btn btn-secondary btn-lg">
      &larr; Back to Dashboard
    </a>
  </div>
</div>

<?php include 'templates/footer.php'; ?>
