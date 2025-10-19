<!-- footer.php - Shared footer included at bottom of pages -->
</div> <!-- end container -->

<footer class="bg-dark text-white mt-5 py-4">
  <div class="container text-center">

    <!-- Quick Links -->
    <div class="mb-3">
      <a href="#" class="text-white me-3"><i class="fas fa-lock"></i> Private</a>
      <a href="contact.php" class="text-white me-3"><i class="fas fa-envelope"></i> Contact us</a>
      <a href="about.php" class="text-white"><i class="fas fa-info-circle"></i> About us</a>
    </div>

    <!-- Social Icons -->
    <div class="mb-3">
      <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
      <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
      <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
      <a href="#" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
    </div>

    <!-- Copyright -->
    <p class="small mb-0">&copy; <?= date("Y") ?> OETMS. All rights reserved.</p>
  </div>
</footer>

<!-- Bootstrap JS (bundle includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
  function openNav() {
    document.getElementById("sidebar").style.width = "250px";
  }
  function closeNav() {
    document.getElementById("sidebar").style.width = "0";
  }
</script>

</body>
</html>
