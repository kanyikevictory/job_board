<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_poster') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: poster_dashboard.php");
    exit();
}

$job_id = intval($_GET['id']);

// Fetch job
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND posted_by = ?");
$stmt->bind_param("ii", $job_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    echo "Job not found.";
    exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $pay = $_POST['pay'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    // Keep old image by default
    $imagePath = $job['image'];

    if (!empty($_FILES['job_image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $imagePath = $targetDir . time() . "_" . basename($_FILES['job_image']['name']);
        move_uploaded_file($_FILES['job_image']['tmp_name'], $imagePath);
    }

    $stmt = $conn->prepare("UPDATE jobs SET title = ?, pay = ?, location = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $title, $pay, $location, $description, $imagePath, $job_id);
    $stmt->execute();

    $_SESSION['success'] = "Job updated successfully!";
    header("Location: poster_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Job - LocalLink UG</title>
    <link rel="stylesheet" href="css/edit_job.css" />
</head>
<body>
<div class="container">
    <div class="form-wrapper">
        <h2>Edit Job</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Job Title</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($job['title']) ?>" required />
            </div>

            <div class="form-group">
                <label for="pay">Pay</label>
                <input type="text" id="pay" name="pay" value="<?= htmlspecialchars($job['pay']) ?>" required />
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($job['location']) ?>" required />
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($job['description']) ?></textarea>
            </div>

            <?php if (!empty($job['image'])): ?>
                <div class="form-group">
                    <label>Current Image:</label><br>
                    <img src="<?= htmlspecialchars($job['image']) ?>" alt="Job Image" style="max-width:200px; margin-bottom:10px;">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="job_image">Update Job Image (optional)</label>
                <input type="file" id="job_image" name="job_image" accept="image/*" />
            </div>

            <div class="form-actions">
                <a href="poster_dashboard.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Update Job</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
