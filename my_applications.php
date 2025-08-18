<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_seeker') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT jobs.title, jobs.location, jobs.pay, applications.applied_at
                        FROM applications 
                        JOIN jobs ON applications.job_id = jobs.id 
                        WHERE applications.user_id = ?
                        ORDER BY applications.applied_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>My Applications - LocalLink UG</title>
    <link rel="stylesheet" href="css/my_applications.css" />
</head>
<body>

<nav class="navbar">
  <div class="container">
    <a href="#" class="brand">LocalLink UG</a>
    <div class="nav-buttons">
      <a href="jobs.php" class="btn btn-light">Browse Jobs</a>
      <a href="logout.php" class="btn btn-outline">Logout</a>
    </div>
  </div>
</nav>

<main class="container">
    <h3>My Job Applications</h3>

    <?php if ($result->num_rows > 0): ?>
        <table class="applications-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Location</th>
                    <th>Pay</th>
                    <th>Applied On</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['pay']) ?></td>
                        <td><?= date('M d, Y H:i', strtotime($row['applied_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert-info">You have not applied for any jobs yet.</div>
    <?php endif; ?>
</main>

</body>
</html>
