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
  <!-- Navigation -->
   <nav class="navbar">
  <div class="nav-container">
    <!-- Logo -->
    <a href="#" class="logo">
      <i class="fas fa-link"></i>
      <span>LocalLink UG</span>
    </a>

    <!-- Desktop Menu -->
    <div class="desktop-menu">
      <a href="index.php">Home</a>
      <a href="jobs.php">Jobs</a>
      <a href="login.php">Post Jobs</a>
      <a href="about.php">About Us</a>
      <a href="contact.php">Contact</a>
      <a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> Login</a>
      <a href="register.php" class="register-btn"><i class="fas fa-user-plus"></i> Register</a>
    </div>

    <!-- Mobile Menu Button -->
    <button id="mobile-menu-button" class="mobile-menu-button">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="mobile-menu hidden ">
    <a href="index.php">Home</a>
    <a href="jobs.php">Jobs</a>
    <a href="login.php">Post Jobs</a>
    <a href="about.php">About Us</a>
    <a href="contact.php">Contact</a>
    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
    <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
  </div>
</nav>


<script>
  const mobileMenuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');

mobileMenuButton.addEventListener('click', () => {
  mobileMenu.classList.toggle('show');
});


    </script>
</body>
</html>