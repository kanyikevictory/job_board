<?php 
session_start();
include 'includes/db.php';

// Only allow job seekers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_seeker') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT jobs.*, users.name AS poster_name FROM jobs 
            JOIN users ON jobs.posted_by = users.id 
            WHERE jobs.title LIKE '%$search%' OR jobs.description LIKE '%$search%' OR jobs.location LIKE '%$search%' 
            ORDER BY jobs.created_at DESC";
} else {
    $sql = "SELECT jobs.*, users.name AS poster_name FROM jobs 
            JOIN users ON jobs.posted_by = users.id 
            ORDER BY jobs.created_at DESC";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Available Jobs - LocalLink UG</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/jobs.css" />
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container navbar-container">
        <h1 class="logo"><i class="fas fa-link"></i>LocalLink UG</h1>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="my_applications.php">My Applications</a>
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <h2>Find Jobs That Match Your Skills</h2>
    <p>Empowering communities through local job opportunities.</p>
</section>

<!-- Message Alerts -->
<?php if (isset($_GET['msg'])): ?>
    <div class="container message-container">
        <?php
        $messages = [
            'applied_success' => '✅ You applied successfully!',
            'already_applied' => '⚠️ You already applied for this job.',
            'apply_failed' => '❌ Application failed. Please try again.',
            'invalid_file' => '⚠️ Invalid file type. Only PDF, DOC, DOCX allowed.',
            'upload_failed' => '❌ CV upload failed. Please try again.',
            'no_file' => '⚠️ Please upload your CV.'
        ];
        echo "<div class='alert alert-info'>" . htmlspecialchars($messages[$_GET['msg']]) . "</div>";
        ?>
    </div>
<?php endif; ?>

<!-- Job Search -->
<div class="container main-content">
    <h2 class="section-title">Available Jobs</h2>

    <form method="GET" class="search-form">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search jobs by title, description, or location..." />
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if (!empty($search)): ?>
            <a href="jobs.php" class="btn btn-clear">Clear</a>
        <?php endif; ?>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <div class="job-grid">
            <?php while($row = $result->fetch_assoc()): ?>
                <?php
                    $posted_at = new DateTime($row['created_at']);
                    $now = new DateTime();
                    $diff = $now->diff($posted_at)->days;
                    $posted_msg = $diff === 0 ? 'today' : "$diff day(s) ago";
                ?>
                <div class="job-card">
                    <?php if (!empty($row['image'])): ?>
    <div class="job-image">
        <img src="uploads/jobs/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
    </div>
<?php endif; ?>


                    <h3 class="job-title"><?= htmlspecialchars($row['title']) ?></h3>
                    <p class="job-description"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                    <p><i class="fa-solid fa-money-bill-wave"></i> <strong>Pay:</strong> <?= htmlspecialchars($row['pay']) ?></p>
                    <p><i class="fa-solid fa-location-dot"></i> <strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                    <p class="job-poster"><i class="fa-solid fa-user"></i> Posted by: <?= htmlspecialchars($row['poster_name']) ?></p>
                    <p class="job-posted-date"><i class="fa-solid fa-calendar-days"></i> Posted <?= $posted_msg ?></p>

                    <?php if ($row['require_cv']): ?>
                        <form action="apply.php" method="post" enctype="multipart/form-data" class="apply-form">
                            <input type="hidden" name="job_id" value="<?= $row['id'] ?>">
                            <label>Upload Your CV:</label>
                            <input type="file" name="cv" accept=".pdf,.doc,.docx" required />
                            <button type="submit" class="btn btn-apply">Apply Now</button>
                        </form>
                    <?php else: ?>
                        <form action="apply.php" method="post" class="apply-form">
                            <input type="hidden" name="job_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-apply">Apply (No CV Required)</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <?php if (!empty($search)): ?>
                No jobs found matching "<strong><?= htmlspecialchars($search) ?></strong>". 
                <a href="jobs.php" class="link">View all jobs</a>
            <?php else: ?>
                No jobs posted yet.
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Back to Top Button -->
<button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="btn-back-to-top"><i class="fa-solid fa-arrow-up"></i></button>

<!-- Testimonial Section -->
<section class="testimonials">
  <div class="container">
    <h3>Success Stories</h3>
    <div class="testimonial-grid">
      
      <div class="testimonial-card">
        <img src="https://i.pravatar.cc/80?img=12" alt="Sarah K." />
        <p>“Thanks to LocalLink UG, I found a local job within days. The platform is simple, fast, and truly helpful!”</p>
        <div class="stars"><i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i></div>
        <p class="testimonial-author">– Sarah K., Kampala</p>
      </div>

      <div class="testimonial-card">
        <img src="https://i.pravatar.cc/80?img=7" alt="Michael O." />
        <p>“I used to struggle finding jobs near me. Now I get alerts and apply easily. It has changed my life.”</p>
        <div class="stars"><i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i></div>
        <p class="testimonial-author">– Michael O., Jinja</p>
      </div>

      <div class="testimonial-card">
        <img src="https://i.pravatar.cc/80?img=25" alt="Grace N." />
        <p>“After university, I didn’t know where to start. A friend told me about LocalLink UG — I landed my first job in two weeks!”</p>
        <div class="stars"><i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i></div>
        <p class="testimonial-author">– Grace N., Mbarara</p>
      </div>

    </div>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container footer-grid">
        <div>
            <h4>LocalLink UG</h4>
            <p>Connecting communities to real job opportunities in Uganda. We help people get hired faster.</p>
        </div>

        <div>
            <h4>Quick Links</h4>
            <ul>
                <li><a href="jobs.php">Find Jobs</a></li>
                <li><a href="my_applications.php">My Applications</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div>
            <h4>Contact Us</h4>
            <p>Email: support@locallink.ug</p>
            <p>Phone: +256 708770829</p>
        </div>
    </div>

    <div class="footer-copy">&copy; <?= date('Y') ?> LocalLink UG. All rights reserved.</div>
</footer>

</body>
</html>
