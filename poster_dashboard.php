<?php
session_start();
include 'includes/db.php';

// Only allow job posters
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_poster') {
    header("Location: login.php");
    exit();
}



if (isset($_GET['delete_job'])) {
    $id = intval($_GET['delete_job']);
    $conn->query("DELETE FROM jobs WHERE id = $id");
    header("Location: poster_dashboard.php");
    exit();
}

$poster_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM jobs WHERE posted_by = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $poster_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php
// Assume user data is stored in session, add defaults if missing
$user_name = $_SESSION['name'] ?? "Job Poster";
$user_email = $_SESSION['email'] ?? "poster@example.com";
// For avatar, you can use Gravatar or a placeholder image
$avatar_url = $_SESSION['user_avatar'] ?? "https://i.pravatar.cc/40?u=".$_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth" >
<head>
    <meta charset="UTF-8" />
    <title>My Posted Jobs - LocalLink UG</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/poster_dashboard.css"> 
</head>
<body class="bg-gray-100  font-sans" x-data="{ sidebarOpen: false }">

<div class="flex h-screen">

  <!-- Sidebar Overlay for mobile -->
  <div
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="sidebar-overlay"
    style="display: none;"
  ></div>

  
 <!-- Sidebar -->
<aside
  :class="sidebarOpen ? 'sidebar-open' : 'sidebar-closed'"
  class="sidebar"
>
  <div class="sidebar-header">
    <i class="fas fa-link"></i> LocalLink UG
  </div>
  <nav class="sidebar-nav">
    <a href="poster_dashboard.php" 
       @click="sidebarOpen = false"
       class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'poster_dashboard.php' ? 'active' : '' ?>">
      <i class="fas fa-tachometer-alt icon"></i> Dashboard
    </a>
    <a href="post_job.php" 
       @click="sidebarOpen = false"
       class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'post_job.php' ? 'active' : '' ?>">
      <i class="fas fa-plus icon"></i> Post Job
    </a>
    <a href="view_applicants.php" 
       @click="sidebarOpen = false"
       class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'view_applicants.php' ? 'active' : '' ?>">
      <i class="fas fa-users icon"></i> Applicants
    </a>
    <a href="logout.php" 
       @click="sidebarOpen = false"
       class="nav-link">
      <i class="fas fa-sign-out-alt icon"></i> Logout
    </a>
    
  </nav>

  <!-- User info at bottom -->
  <div class="sidebar-userinfo">
    <img src="<?= htmlspecialchars($avatar_url) ?>" alt="User Avatar" class="avatar" />
    <div>
      <p class="user-name"><?= htmlspecialchars($user_name) ?></p>
      <p class="user-email"><?= htmlspecialchars($user_email) ?></p>
    </div>
  </div>
</aside>


  <!-- Main content wrapper -->
  <div class="main-content">

    <!-- Top navbar -->
    <header class="top-navbar">
      <button
        @click="sidebarOpen = true"
        class="sidebar-toggle"
        aria-label="Open sidebar"
      >
        <i class="fas fa-bars fa-lg"></i>
      </button>
      <div class="topbar-title">LocalLink UG</div>
      <div></div> <!-- empty div to balance flex -->
    </header>

    <!-- Main content area -->
    <main class="main-area">
      <h2 class="page-title">My Posted Jobs</h2>

      <?php if (isset($_SESSION['success'])): ?>
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
             x-transition
             class="alert success-alert">
          <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <?php if ($result->num_rows > 0): ?>
        <div class="jobs-grid">
          <?php while($job = $result->fetch_assoc()): ?>
  <div class="job-card">
    <?php if (!empty($job['image'])): ?>
      <div class="job-image">
        <img src="<?= htmlspecialchars($job['image']) ?>" alt="Job Image" style="max-width:100%; height:auto; border-radius:8px;">
      </div>
    <?php endif; ?>

    <div>
      <h3 class="job-title"><?= htmlspecialchars($job['title']) ?></h3>
      <p class="job-detail"><strong>Pay:</strong> <?= htmlspecialchars($job['pay']) ?></p>
      <p class="job-detail"><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
      <p class="job-date">
        <i class="far fa-calendar-alt calendar-icon"></i> <?= date('M d, Y', strtotime($job['created_at'])) ?>
      </p>
    </div>

    <div class="job-actions">
      <a href="edit_job.php?id=<?= $job['id'] ?>" 
         class="action-link edit-link">
        <i class="fas fa-edit"></i> Edit
      </a>
      <a href="?delete_job=<?= $job['id'] ?>" 
         onclick="return confirm('Delete this job?')"
         class="action-link delete-link">
        <i class="fas fa-trash-alt"></i> Delete
      </a>
    </div>
  </div>
<?php endwhile; ?>

        </div>
      <?php else: ?>
        <div class="no-jobs-message">
          <i class="fas fa-info-circle info-icon"></i>
          <p>You haven't posted any jobs yet.</p>
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<!-- Footer -->
<footer class="footer">
  <div class="footer-content">
    <p>&copy; 2025 LocalLink UG. All rights reserved.</p>
    <div class="footer-links">
      <a href="#" class="footer-link">Privacy Policy</a>
      <a href="#" class="footer-link">Terms</a>
      <a href="#" class="footer-link">Help</a>
    </div>
  </div>
</footer>

</body>
</html>
