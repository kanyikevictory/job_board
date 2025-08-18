<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Welcome to LocalLink UG</title>
  <link rel="stylesheet" href="css/index.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- Hero Section-->
  <div class="gradient-bg">
    <section class="hero-section">
        <div class="hero-overlay">
            <div class="hero-text">
                <h1>Welcome to LocalLink UG</h1>
                <p>
                    Empowering your community by connecting job seekers with local
                    opportunities.
                </p>

                <!-- Unified button group -->
                <div class="button-group">
                    <a href="poster_dashboard.php" class="btn btn-warning">
                        Post a Job Now
                    </a>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-rocket"></i> Get Started
                    </a>
                    <a href="login.php" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>


  <!-- Features Section -->
  <section class="features-section">
    <div class="features-container">
      <header class="features-header">
        <h2>Key Features</h2>
        <p>
          Why Choose LocalLink UG
        </p>
        <p>
          Our platform is designed to bring your district’s job market to your fingertips.
        </p>
      </header>

      <div class="features-grid">
        <!-- Feature 1 -->
        <article class="card">
          <div class="card-icon job-posters">
            <i class="fas fa-briefcase"></i>
          </div>
          <h3>Job Posters</h3>
          <p>Easily post jobs and reach thousands of local job seekers.</p>
          <a href="login.php">
            Get Started <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </article>

        <!-- Feature 2 -->
        <article class="card">
          <div class="card-icon admin">
            <i class="fas fa-users-cog"></i>
          </div>
          <h3>Admin Controls</h3>
          <p>Manage job listings and monitor applications efficiently.</p>
          <a href="login.php">
            Learn More <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </article>

        <!-- Feature 3 -->
        <article class="card">
          <div class="card-icon job-seekers">
            <i class="fas fa-user-check"></i>
          </div>
          <h3>Job Seekers</h3>
          <p>Find and apply for jobs that fit your skills and location.</p>
          <a href="register.php">
            Join Now <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </article>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="testimonials-section">
    <div class="testimonials-container">
      <header class="testimonials-header">
        <h2>Testimonials</h2>
        <p>What Our Users Say</p>
      </header>

      <div class="testimonials-grid">
        <!-- Testimonial 1 -->
        <article class="testimonial-card">
          <header class="testimonial-header">
            <div class="testimonial-avatar blue">
              <i class="fas fa-quote-left"></i>
            </div>
            <div class="testimonial-info">
              <h4>Jane Doe</h4>
              <p>Job Seeker</p>
            </div>
          </header>
          <p class="testimonial-text">
            LocalLink UG helped me find a great job within days! The process was easy and fast.
          </p>
          <div class="testimonial-stars">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i
              class="fas fa-star"></i
            ><i class="fas fa-star"></i><i class="fas fa-star"></i>
          </div>
        </article>

        <!-- Testimonial 2 -->
        <article class="testimonial-card">
          <header class="testimonial-header">
            <div class="testimonial-avatar green">
              <i class="fas fa-quote-left"></i>
            </div>
            <div class="testimonial-info">
              <h4>John Smith</h4>
              <p>Job Poster</p>
            </div>
          </header>
          <p class="testimonial-text">
            Posting jobs and managing applicants has never been easier. Highly recommend!
          </p>
          <div class="testimonial-stars">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i
              class="fas fa-star"></i
            ><i class="fas fa-star"></i><i class="fas fa-star"></i>
          </div>
        </article>

        <!-- Testimonial 3 -->
        <article class="testimonial-card">
          <header class="testimonial-header">
            <div class="testimonial-avatar purple">
              <i class="fas fa-quote-left"></i>
            </div>
            <div class="testimonial-info">
              <h4>Alice Johnson</h4>
              <p>Administrator</p>
            </div>
          </header>
          <p class="testimonial-text">
            The admin panel is intuitive and powerful. It makes managing our district’s jobs a breeze.
          </p>
          <div class="testimonial-stars">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i
              class="fas fa-star"></i
            ><i class="fas fa-star"></i><i class="fas fa-star"></i>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- Call to Action Section -->
  <section class="cta-section">
    <div class="cta-container">
      <h2 class="cta-heading">
        Ready to help your community? <br />
        <span class="light-text">Join LocalLink UG today!</span>
      </h2>
      <div class="cta-buttons">
        <a href="register.php" class="cta-button-primary">Register Now</a>
        <a href="login.php" class="cta-button-secondary">Login</a>
      </div>
    </div>
  </section>

  <?php include 'footer.php';?>

  <script>
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
      mobileMenu.classList.toggle('show');
    });

    // Set current year in footer
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
