<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>OETMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap + Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0; padding: 0;
      background-color: #ee86f4ff;
      scroll-behavior: smooth;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background: linear-gradient(135deg, #8b0f0fff, #fbfefdff);

    }

    /* NAVBAR */
    .navbar {
      background: #2c3e50;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .navbar-brand {
      color: #eeadadff !important;
      font-weight: 700;
      font-size: 1.4rem;
    }
    .navbar .btn {
      border-radius: 20px;
      margin-left: 10px;
      font-weight: 500;
      padding: 6px 16px;
    }
    .btn-login { background: #6a82fb; color: #fff; }
    .btn-login:hover { background: #fc5c7d; color: #fff; }
    .btn-signup { background: #f7971e; color: #fff; }
    .btn-signup:hover { background: #ffd200; color: #000; }
    .btn-logout { background: #e74c3c; color: #fff; }
    .btn-logout:hover { background: #c0392b; color: #fff; }

    /* HERO */
    .hero {
      background: url("images/indexback.png") no-repeat center center/cover;
      color: #fff; text-align: center;
      padding: 100px 20px 70px;
    }
    .hero h1 { font-size: 3rem; font-weight: bold; }
    .hero p { font-size: 1.2rem; margin: 15px 0; }

    /* BROWSE EVENTS */
    .browse-section {
      text-align: center;
      padding: 40px 20px;
      background: #f8f9fa;
    }
    .browse-section .btn {
      padding: 14px 34px;
      border-radius: 30px;
      font-weight: 600;
      font-size: 1.1rem;
      background: #6a82fb;
      color: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .browse-section .btn:hover {
      background: #fc5c7d;
      color: #fff;
    }

    /* FEATURES */
    .features {
      background: linear-gradient(135deg, #fc5c7d, #6a82fb); padding: 60px 20px;
    }
    .features h2 { text-align: center; margin-bottom: 40px; }
    .feature-box {
      background: #f8f9fa; 
      border-radius: 12px;
      padding: 25px; text-align: center;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      transition: transform 0.3s;
    }
    .feature-box:hover { transform: translateY(-8px); }
    .feature-box i {
      font-size: 40px; color: #fc5c7d; margin-bottom: 15px;
    }

    /* REVIEWS */
    .reviews {
      background: linear-gradient(135deg, #a18cd1, #fbc2eb);
      padding: 60px 20px; text-align: center; color: #333;
    }
    .reviews h2 { color: #fff; margin-bottom: 30px; }
    .review-card {
      background: #fff; padding: 20px;
      border-radius: 14px; margin: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    .review-card:hover { transform: scale(1.03); }
    .review-card p { font-style: italic; }
    .review-card h4 { margin-top: 12px; font-weight: 600; }

    /* CONTACT */
    .contact {
      padding: 60px 20px;
      background: #2c3e50; color: #fff;
      background:
      text-align: center;
    }
    .contact h2 { margin-bottom: 25px; }
    .contact-info {
      display: flex; justify-content: center; flex-wrap: wrap; gap: 30px;
    }
    .contact-box {
      background: #34495e; padding: 20px; border-radius: 12px;
      width: 250px; text-align: center;
      transition: transform 0.3s;
    }
    .contact-box:hover { transform: translateY(-5px); }  
    .contact-box i { font-size: 28px; margin-bottom: 10px; color: #ffd200; }
    .contact-box a { color: #fff; text-decoration: none; }
    .contact-box a:hover { text-decoration: underline; }

    /* FOOTER */
    footer {
      background: #1a252f; color: #ccc;
      text-align: center; padding: 18px;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="fa-solid fa-ticket"></i>  <b>EVENT EASE</b></a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <?php if (!empty($_SESSION['role'])): ?>
          <li class="nav-item">
            <a class="btn btn-logout" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="btn btn-login" href="signin.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-signup" href="signup.php"><i class="fa-solid fa-user-plus"></i> Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <h1>Experience Events Like Never Before</h1>
  <p>Book tickets, discover events, and enjoy seamless online ticket management with <b>EVENT EASE</b>.</p>
</section>

<!-- BROWSE EVENTS -->
<section class="browse-section">
  <a href="browse_events.php" class="btn"><i class="fa-solid fa-magnifying-glass"></i> BROWSE EVENTS</a>
</section>

<!-- FEATURES -->
<section class="features">
  <div class="container">
   <!-- Why Choose Us Section -->
<section id="why-choose-us" class="py-5" style="background: url('images/indexback.png') no-repeat center/cover;">
  <div class="container text-center">
    <h2 class="fw-bold mb-4 text-primary" data-aos="fade-up">Why Choose EVENT EASE</h2>
    <p class="text-muted mb-5" style="max-width: 700px; margin: auto;" data-aos="fade-up" data-aos-delay="100">
      At <strong>EVENT EASE</strong>, we redefine how events are organized, managed, and experienced. 
      Our platform is designed with a focus on simplicity, efficiency, and security — helping event organizers and attendees 
      enjoy a smooth, stress-free experience from start to finish.
    </p>

    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
        <div class="p-4 rounded shadow-sm h-100" style="background: #fff;">
          <div class="mb-3 text-primary"><i class="fa fa-bolt fa-2x"></i></div>
          <h5 class="fw-semibold">Fast and Seamless Booking</h5>
          <p class="text-muted">
            Say goodbye to long queues and complex forms. With <strong>EVENT EASE</strong>, you can browse events, book tickets, and 
            get instant confirmations — all within seconds. Every click is optimized for speed and convenience.
          </p>
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-up" data-aos-delay="250">
        <div class="p-4 rounded shadow-sm h-100" style="background: #fff;">
          <div class="mb-3 text-primary"><i class="fa fa-lock fa-2x"></i></div>
          <h5 class="fw-semibold">Secure Payments and Data</h5>
          <p class="text-muted">
            Your safety is our top priority. <strong>EVENT EASE</strong> integrates trusted payment gateways and strong encryption to protect 
            your personal and financial data. Every transaction is handled securely to give you complete peace of mind.
          </p>
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-up" data-aos-delay="350">
        <div class="p-4 rounded shadow-sm h-100" style="background: #fff;">
          <div class="mb-3 text-primary"><i class="fa fa-calendar-check fa-2x"></i></div>
          <h5 class="fw-semibold">Effortless Event Management</h5>
          <p class="text-muted">
            Organizers can easily create, publish, and monitor events using our user-friendly dashboard. 
            From ticket sales to attendance tracking, EVENT EASE gives you full control at your fingertips.
          </p>
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-up" data-aos-delay="450">
        <div class="p-4 rounded shadow-sm h-100" style="background: #fff;">
          <div class="mb-3 text-primary"><i class="fa fa-users fa-2x"></i></div>
          <h5 class="fw-semibold">Real-Time Attendee Insights</h5>
          <p class="text-muted">
            Gain powerful insights into your audience with live analytics. Track attendance, engagement, 
            and preferences in real-time to make data-driven improvements for future events.
          </p>
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-up" data-aos-delay="550">
        <div class="p-4 rounded shadow-sm h-100" style="background: #fff;">
          <div class="mb-3 text-primary"><i class="fa fa-headset fa-2x"></i></div>
          <h5 class="fw-semibold">Dedicated Support</h5>
          <p class="text-muted">
            Our support team is always here for you — from setting up events to resolving attendee questions. 
            Expect timely responses and personalized assistance whenever you need it.
          </p>
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-up" data-aos-delay="650">
        <div class="p-4 rounded shadow-sm h-100" style="background: #fff;">
          <div class="mb-3 text-primary"><i class="fa fa-star fa-2x"></i></div>
          <h5 class="fw-semibold">Trusted by Many</h5>
          <p class="text-muted">
            From concerts to conferences, E has powered countless successful events. 
            Our reliability, transparency, and innovation make us the preferred choice 
            for event professionals and attendees alike.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- AOS Library -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    offset: 120,
    once: true
  });
</script>

<!-- REVIEWS -->
<section class="reviews">
  <div class="container">
    <h2>What People Say</h2>
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="review-card">
          <p>"Super easy to book tickets, and the payment was smooth!"</p>
          <h4>- Sarah K.</h4>
        </div>
      </div>
      <div class="col-md-4">
        <div class="review-card">
          <p>"I love the design and how simple the process is. Great job!"</p>
          <h4>- John M.</h4>
        </div>
      </div>
      <div class="col-md-4">
        <div class="review-card">
          <p>"Finally, an event system that just works. Highly recommended!"</p>
          <h4>- Aisha O.</h4>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CONTACT -->
<section class="contact">
  <h2>Contact Us</h2><br>
  <p align="center">If you have any questions or need assistance, feel free to reach out to us through any of the channels below:</p><br>
  <div class="contact-info">
    <div class="contact-box">
      <i class="fa-brands fa-whatsapp"></i>
      <p><a href="https://wa.me/254740443752" target="_blank">Chat on WhatsApp</a></p>
    </div>
    <div class="contact-box">
      <i class="fa-solid fa-envelope"></i>
      <p><a href="mailto:info@eventease.com">info@eventease.com</a></p>
      <p><a href="mailto:support@eventease.com">support@eventease.com</a></p>
    </div>
    <div class="contact-box">
      <i class="fa-solid fa-phone"></i>
      <p><a href="tel:+254740443752">+254 740 443 752</a></p>
    </div>
  </div>
</section>
<!-- FOOTER -->
<?php include 'templates/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
