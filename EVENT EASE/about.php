<?php
// about.php — About Page for OETMS
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us | OETMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fc;
      color: #333;
    }

    /* Hero Section */
    .hero {
      background: linear-gradient(135deg, #d023b9ff, #7024bcff);
      color: white;
      padding: 100px 20px 80px;
      text-align: center;
    }

    .hero h1 {
      font-size: 2.8rem;
      font-weight: bold;
    }

    .hero p {
      font-size: 1.1rem;
      max-width: 700px;
      margin: 15px auto 0;
    }

    /* About Content */
    .about-content {
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 40px;
      margin-top: -50px;
      position: relative;
      z-index: 10;
    }

    .about-content p {
      font-size: 1.05rem;
      color: #555;
      line-height: 1.7;
    }

    /* Why Choose Us Cards */
    .why-choose-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      height: 100%;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .why-choose-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    .why-choose-card i {
      color: #7024bcff;
      margin-bottom: 12px;
    }

    /* Mission & Vision */
    .mission-section {
      background: #7024bcff;
      color: white;
      border-radius: 15px;
      padding: 50px 30px;
      margin-top: 60px;
    }

    .mission-section h3 {
      color: #fff;
      margin-bottom: 20px;
    }

    /* CTA Footer */
    .cta-section {
      background: linear-gradient(135deg, #d023b9ff, #7024bcff);
      color: white;
      padding: 50px 20px;
      text-align: center;
      border-radius: 0;
      margin-top: 80px;
    }

    footer {
      background: #111;
      color: #aaa;
      text-align: center;
      padding: 15px;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<!-- Hero -->
<section class="hero" data-aos="fade-down">
  <div class="container">
    <h1>About <span class="text-warning">EVENT EASE</span></h1>
    <p>Your trusted partner in managing events and simplifying ticketing for a smarter, faster, and more secure experience.</p>
  </div>
</section>

<!-- About Content -->
<div class="container">
  <div class="about-content" data-aos="fade-up">
    <h3 class="fw-bold text-center text-primary mb-3">Who We Are</h3>
    <p>
      The <strong>EVENT EASE</strong> is a cutting-edge platform developed to transform the way events are organized, managed, and experienced in the modern digital world. Founded with the goal of eliminating the frustrations of manual event coordination and long queues for ticket purchases, EVENT EASE delivers an automated, secure, and seamless process for both event organizers and attendees.
    </p>
    <p>
      We are a passionate team of innovators, developers, and event enthusiasts who believe that technology should make human experiences more meaningful, not more complicated. That’s why EVENT EASE combines user-friendly design, smart analytics, and reliable ticketing tools into one complete system — empowering organizers to plan efficiently while offering attendees a fast and reliable way to book, pay for, and manage their event participation.
    </p>
    <p>
      At the heart of EVENT EASE is a mission to create <em>connections</em> — between people, experiences, and opportunities. Whether it’s a music festival, business seminar, charity fundraiser, or academic workshop, our system ensures that every interaction is smooth, secure, and memorable. We are not just a ticketing platform — we are a digital bridge that connects event visionaries with their audiences, and helps every event, big or small, reach its full potential.
    </p>
    <p>
      Our ongoing commitment to excellence drives us to continuously improve our services, integrate new technologies, and maintain transparency and trust with our users. With EVENT EASE, you can rest assured that your event journey — from creation to celebration — is powered by innovation, simplicity, and integrity.
    </p>
  </div>
</div>

<!-- Why Choose Us -->
<section class="py-5" id="why-choose-us">
  <div class="container text-center">
    <h2 class="fw-bold mb-4 text-primary" data-aos="fade-up">Why Choose Us</h2>
    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="why-choose-card h-100">
          <i class="fa fa-bolt fa-2x"></i>
          <h5 class="fw-semibold">Fast and Effortless</h5>
          <p>Our intuitive design lets you browse events, reserve seats, and confirm tickets in seconds — all optimized for speed and convenience.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="why-choose-card h-100">
          <i class="fa fa-lock fa-2x"></i>
          <h5 class="fw-semibold">Secure and Reliable</h5>
          <p>We use strong encryption and trusted payment gateways to ensure your transactions and data remain fully protected at all times.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
        <div class="why-choose-card h-100">
          <i class="fa fa-users fa-2x"></i>
          <h5 class="fw-semibold">User-Focused Experience</h5>
          <p>Every feature is designed to enhance your journey — from seamless navigation to real-time event insights and customer support.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Mission & Vision -->
<div class="container">
  <div class="mission-section mt-5 text-center" data-aos="fade-up">
    <div class="row align-items-center">
      <div class="col-md-6 mb-4 mb-md-0">
        <h3><i class="fa fa-bullseye me-2"></i> Our Mission</h3>
        <p>
          To simplify event management and make digital ticketing accessible, efficient, and secure for everyone — from small gatherings to global conferences.
        </p>
      </div>
      <div class="col-md-6">
        <h3><i class="fa fa-eye me-2"></i> Our Vision</h3>
        <p>
          To become the leading digital platform for event management, known for reliability, innovation, and exceptional user satisfaction worldwide.
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Call to Action -->
<section class="cta-section" data-aos="zoom-in">
  <h2 class="fw-bold mb-3">Ready to Experience the Future of Event Management?</h2>
  <p class="mb-4">Join OETMS today and simplify how you manage, attend, and enjoy your events!</p>
  <a href="signup.php" class="btn btn-light btn-lg px-4"><i class="fa fa-user-plus me-2"></i> Get Started</a>
</section>
<a href="logout.php" 
   class="btn btn-danger"
   onclick="return confirm('Are you sure you want to log out?');">
   <i class="fas fa-sign-out-alt"></i> Logout
</a>

<footer>
  &copy; <?= date("Y"); ?> OETMS — All Rights Reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration: 800, offset: 120, once: true });
</script>
</body>
</html>
