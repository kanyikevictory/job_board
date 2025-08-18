<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_seeker') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = intval($_POST['job_id']);
    $user_id = $_SESSION['user_id'];

    // Check if already applied
    $check = $conn->prepare("SELECT * FROM applications WHERE job_id = ? AND user_id = ?");
    $check->bind_param("ii", $job_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        header("Location: jobs.php?msg=already_applied");
        exit();
    }

    // Check if job requires CV
    $jobData = $conn->prepare("SELECT require_cv FROM jobs WHERE id = ?");
    $jobData->bind_param("i", $job_id);
    $jobData->execute();
    $jobInfo = $jobData->get_result()->fetch_assoc();
    $require_cv = $jobInfo['require_cv'];

    if ($require_cv == 1) {
        // CV required: check and upload file
        if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
            header("Location: jobs.php?msg=no_file");
            exit();
        }

        $allowed = ['pdf', 'doc', 'docx'];
        $filename = $_FILES['cv']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            header("Location: jobs.php?msg=invalid_file");
            exit();
        }

        $newName = uniqid('cv_', true) . "." . $ext;
        $uploadDir = 'uploads/cvs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $uploadPath = $uploadDir . $newName;

        if (!move_uploaded_file($_FILES['cv']['tmp_name'], $uploadPath)) {
            header("Location: jobs.php?msg=upload_failed");
            exit();
        }

        // Insert application with CV path
        $insert = $conn->prepare("INSERT INTO applications (job_id, user_id, applied_at, cv_path) VALUES (?, ?, NOW(), ?)");
        $insert->bind_param("iis", $job_id, $user_id, $uploadPath);
        $success = $insert->execute();

    } else {
        // CV not required: insert without cv_path
        $insert = $conn->prepare("INSERT INTO applications (job_id, user_id, applied_at) VALUES (?, ?, NOW())");
        $insert->bind_param("ii", $job_id, $user_id);
        $success = $insert->execute();
    }

    if ($success) {
        // Send email notification to job poster
        $stmt = $conn->prepare("SELECT j.title, u.email, u.name FROM jobs j JOIN users u ON j.posted_by = u.id WHERE j.id = ?");
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $job = $stmt->get_result()->fetch_assoc();

        $seeker = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
        $seeker->bind_param("i", $user_id);
        $seeker->execute();
        $seeker_info = $seeker->get_result()->fetch_assoc();

        $to = $job['email'];
        $subject = "📩 New Application for: " . $job['title'];
        $message = "Hello " . $job['name'] . ",\n\n" .
                   "You have received a new application for your job posting: " . $job['title'] . ".\n\n" .
                   "Applicant Name: " . $seeker_info['name'] . "\n" .
                   "Applicant Email: " . $seeker_info['email'] . "\n\n" .
                   "Please log in to your dashboard to view more details.\n\n" .
                   "— LocalLink UG";

        $headers = "From: no-reply@locallinkug.com";
        mail($to, $subject, $message, $headers);

        header("Location: jobs.php?msg=applied_success");
    } else {
        header("Location: jobs.php?msg=apply_failed");
    }

    exit();

} else {
    header("Location: jobs.php");
    exit();
}
?>