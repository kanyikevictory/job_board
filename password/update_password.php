<?php
// update_password.php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["token"] ?? '';
    $password = $_POST["password"] ?? '';
    $confirm = $_POST["confirm"] ?? '';

    // Check if passwords match
    if ($password !== $confirm) {
        die("❌ Passwords do not match.");
    }

    // Validate token again
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        die("❌ Invalid or expired token.");
    }

    $row = $result->fetch_assoc();
    $user_id = $row["id"];

    // Hash the new password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update password and clear token
    $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
    $update->bind_param("si", $hashedPassword, $user_id);
    
    if ($update->execute()) {
        echo "✅ Password reset successful. You can now <a href='login.php'>log in</a>.";
    } else {
        echo "❌ Failed to update password.";
    }
}
?>
