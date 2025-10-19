<?php
session_start();
include 'templates/header.php';
include 'db.php';

if(!isset($_SESSION['user_id'])) {
  header("Location: signin.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$result = null;

if ($user_role === 'organizer') {
    // Organizer: show tickets purchased for their events
    $sql = "
      SELECT tickets.id, users.name AS attendee, events.title, tickets.purchase_date
      FROM tickets
      INNER JOIN users ON tickets.user_id = users.id
      INNER JOIN events ON tickets.event_id = events.id
      WHERE events.organizer_id=?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif ($user_role === 'attendee') {
    // Attendee: show tickets they purchased
    $sql = "
      SELECT tickets.id, events.title, tickets.purchase_date
      FROM tickets
      INNER JOIN events ON tickets.event_id = events.id
      WHERE tickets.user_id=?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "<p>Invalid role detected.</p>";
    include 'templates/footer.php';
    exit;
}
?>

<div class="container my-4">
  <div class="card shadow border-0">
    <div class="card-body">
      <h2 class="fw-bold text-primary mb-3">
        <i class="fas fa-ticket-alt"></i> Manage Tickets
      </h2>

      <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <?php if ($user_role === 'organizer'): ?>
                  <th><i class="fas fa-user"></i> Attendee</th>
                  <th><i class="fas fa-calendar-check"></i> Event</th>
                  <th><i class="fas fa-clock"></i> Purchased On</th>
                <?php else: ?>
                  <th><i class="fas fa-calendar-check"></i> Event</th>
                  <th><i class="fas fa-clock"></i> Purchased On</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <?php if ($user_role === 'organizer'): ?>
                    <td><?= htmlspecialchars($row['attendee']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= $row['purchase_date'] ?></td>
                  <?php else: ?>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= $row['purchase_date'] ?></td>
                  <?php endif; ?>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i> No tickets purchased yet.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>