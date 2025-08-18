<?php
session_start();
include '../includes/db.php';

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get admin info
$admin_id = $_SESSION['user_id'];
$admin = $conn->query("SELECT * FROM users WHERE id = $admin_id")->fetch_assoc();

// Delete job
if (isset($_GET['delete_job'])) {
    $id = intval($_GET['delete_job']);
    $conn->query("DELETE FROM jobs WHERE id = $id");
    header("Location: dashboard.php");
    exit();
}

// Delete user
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: dashboard.php");
    exit();
}

// Fetch users and jobs
$users = $conn->query("SELECT * FROM users");
$jobs = $conn->query("SELECT jobs.*, users.name AS poster FROM jobs JOIN users ON jobs.posted_by = users.id");

// Fix applications query to join users and jobs and fetch job title and applicant name
$apps = $conn->query("
    SELECT 
        applications.*, 
        users.name AS applicant, 
        jobs.title 
    FROM applications 
    JOIN users ON applications.user_id = users.id
    JOIN jobs ON applications.job_id = jobs.id
");

// Count totals
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role != 'admin'")->fetch_assoc()['total'];
$total_jobs = $conn->query("SELECT COUNT(*) AS total FROM jobs")->fetch_assoc()['total'];
$total_apps = $conn->query("SELECT COUNT(*) AS total FROM applications")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard - LocalLink UG</title>
    <link rel="stylesheet" href="dashboard.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="flex ">
   <!-- Sidebar -->
<aside>
    <!-- Logo -->
    <div>
        <h1>LocalLink UG</h1>
    </div>

    <!-- Navigation -->
    <nav>
        <a href="dashboard.php">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m-8 0h16" />
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="#users">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M12 11a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
            <span>Users</span>
        </a>
        <a href="#jobs">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 17v-6h13v6M9 5v4h13V5M3 5h.01M3 9h.01M3 13h.01M3 17h.01" />
            </svg>
            <span>Jobs</span>
        </a>
        <a href="#applicants">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8 9l3 3-3 3m5-6h8" />
            </svg>
            <span>Applications</span>
        </a>
        <a href="../index.php">
            <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8 9l3 3-3 3m5-6h8" />
            </svg>
            <span>Home</span>
        </a>
    </nav>

    <!-- User Info -->
    <div>
        <img src="https://i.pravatar.cc/50" alt="Avatar" />
        <div>
            <p><?= htmlspecialchars($admin['name']) ?></p>
            <p>admin@locallink.ug</p>
        </div>
    </div>
</aside>

<!-- Main Content -->
<main>
    <h1>Admin Dashboard</h1>

    
  <!-- Summary -->
<div class="grid summary-grid">
    <div class="summary-card bg-blue">
        <i class="fas fa-users fa-2x summary-icon"></i>
        <div>
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
        </div>
    </div>

    <div class="summary-card bg-green">
        <i class="fas fa-briefcase fa-2x summary-icon"></i>
        <div>
            <h3>Jobs Posted</h3>
            <p><?= $total_jobs ?></p>
        </div>
    </div>

    <div class="summary-card bg-yellow">
        <i class="fas fa-file-alt fa-2x summary-icon"></i>
        <div>
            <h3>Applications</h3>
            <p><?= $total_apps ?></p>
        </div>
    </div>
</div>

    <!-- Users Table -->
    <h2 id="users">All Users</h2>
    <div class="overflow-x-auto mb-12">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['role'] ?></td>
                    <td class="text-center">
                        <?php if ($user['role'] !== 'admin'): ?>
                            <a href="?delete_user=<?= $user['id'] ?>" class="text-red-600" onclick="return confirm('Delete this user?')">Delete</a>
                        <?php else: ?>
                            <span class="text-gray-400">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Jobs Table -->
    <h2 id="jobs">All Posted Jobs</h2>
    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Pay</th>
                    <th>Location</th>
                    <th>Posted By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($job = $jobs->fetch_assoc()): ?>
                <tr>
                    <td><?= $job['id'] ?></td>
                    <td><?= htmlspecialchars($job['title']) ?></td>
                    <td><?= htmlspecialchars($job['pay']) ?></td>
                    <td><?= htmlspecialchars($job['location']) ?></td>
                    <td><?= htmlspecialchars($job['poster']) ?></td>
                    <td class="text-center">
                        <a href="?delete_job=<?= $job['id'] ?>" class="text-red-600" onclick="return confirm('Delete this job?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Applicants Table -->
    <h2 id="applicants">All applicants</h2>
    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Job</th>
                    <th>Applicant</th>
                    <th>Date</th>
                    <th>CV</th>
                </tr>
            </thead>
            <tbody>
                <?php while($app = $apps->fetch_assoc()): ?>
                <tr>
                    <td><?= $app['id'] ?></td>
                    <td><?= htmlspecialchars($app['title']) ?></td>
                    <td><?= htmlspecialchars($app['applicant']) ?></td>
                    <td><?= htmlspecialchars($app['applied_at']) ?></td>
                    <td>
                        <?php if (!empty($app['cv_path'])): ?>
                            <a href="<?= htmlspecialchars($app['cv_path']) ?>" target="_blank" class="text-blue-600">View CV</a>
                        <?php else: ?>
                            No CV
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</main>
</div>

</body>
</html>
