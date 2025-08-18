<?php
$conn = new mysqli("localhost", "root", "", "job_board");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
