<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_poster') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $pay = $_POST['pay'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $require_cv = isset($_POST['require_cv']) ? 1 : 0;
    $posted_by = $_SESSION['user_id'];

    // Handle image upload
    $imagePath = null;
    if (!empty($_FILES['job_image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $imagePath = $targetDir . time() . "_" . basename($_FILES['job_image']['name']);
        move_uploaded_file($_FILES['job_image']['tmp_name'], $imagePath);
    }

    $stmt = $conn->prepare("INSERT INTO jobs (title, pay, location, description, require_cv, posted_by, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiis", $title, $pay, $location, $description, $require_cv, $posted_by, $imagePath);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Job posted successfully!";
        header("Location: poster_dashboard.php");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Job - LocalLink UG</title>
    <link rel="stylesheet" href="css/post_job.css">
</head>
<body>
<div class="container">
    <div class="form-wrapper">
        <h2>Post a Job</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Job Title</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="pay">Pay</label>
                <input type="text" id="pay" name="pay" required>
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="job_image">Upload Job Image (optional)</label>
                <input type="file" id="job_image" name="job_image" accept="image/*">
            </div>

            <div class="form-group">
                <input type="checkbox" id="require_cv" name="require_cv">
                <label for="require_cv">Require CV upload</label>
            </div>

            <div class="form-actions">
                <a href="poster_dashboard.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Post Job</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
