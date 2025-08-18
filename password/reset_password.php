<?php
// reset_password.php
include '../includes/db.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die("❌ Invalid or missing token.");
}

// Check if token is valid and not expired
$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("❌ Token is invalid or expired.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow p-4">
          <h3 class="text-center mb-4">🔐 Reset Your Password</h3>
          <form action="update_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="mb-3">
              <label for="password" class="form-label">New Password</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="confirm" class="form-label">Confirm New Password</label>
              <input type="password" name="confirm" id="confirm" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Reset Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
