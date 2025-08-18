<?php 
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_poster') {
    header("Location: login.php");
    exit();
}

$poster_id = $_SESSION['user_id'];

// Fetch all jobs posted by this user
$jobs_stmt = $conn->prepare("SELECT * FROM jobs WHERE posted_by = ?");
$jobs_stmt->bind_param("i", $poster_id);
$jobs_stmt->execute();
$jobs_result = $jobs_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicants - LocalLink UG</title>
    <link rel="stylesheet" href="css/view_applicants.css">
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Navbar -->
<nav class="navbar">
    <div class="container flex-between">
        <h1 class="logo">LocalLink UG</h1>
        <div class="nav-links">
            <a href="poster_dashboard.php" class="btn-primary">Dashboard</a>
            <a href="logout.php" class="btn-outline">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container main-content">
    <h2 class="page-title">Applicants to Your Jobs</h2>

    <?php while ($job = $jobs_result->fetch_assoc()): ?>
        <div class="card">
            <div class="card-header">
                <h3><?php echo htmlspecialchars($job['title']); ?> – <?php echo htmlspecialchars($job['location']); ?></h3>
            </div>
            <div class="card-body">
                <?php
                $job_id = $job['id'];
                $apps_stmt = $conn->prepare("SELECT users.name, users.email, users.telephone, applications.cv_path, applications.applied_at FROM applications JOIN users ON applications.user_id = users.id WHERE applications.job_id = ?");
                $apps_stmt->bind_param("i", $job_id);
                $apps_stmt->execute();
                $apps = $apps_stmt->get_result();
                ?>

                <?php if ($apps->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="applicants-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Telephone</th>
                                    <th>CV</th>
                                    <th>Applied On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($applicant = $apps->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($applicant['name']); ?></td>
                                        <td><?php echo htmlspecialchars($applicant['email']); ?></td>
                                        <td><?php echo htmlspecialchars($applicant['telephone']); ?></td>
                                        <td>
                                            <?php 
                                            $cv_path = $applicant['cv_path'];
                                            // Only show CV link if file exists
                                            if (!empty($cv_path) && strpos($cv_path, 'uploads/cvs/') === 0 && file_exists($cv_path)): ?>
                                                <a href="<?php echo htmlspecialchars($cv_path); ?>" target="_blank" class="cv-link">View CV</a>
                                            <?php else: ?>
                                                <span class="no-cv">No CV</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($applicant['applied_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-applicants">No applicants yet.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
