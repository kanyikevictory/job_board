<?php
session_start();
include 'includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: Admin/dashboard.php");
            } elseif ($user['role'] == 'job_poster') {
                header("Location: poster_dashboard.php");
            } else {
                header("Location: jobs.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - MyDistrictJobs</title>
  <link rel="stylesheet" href="css/login.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
<?php include 'navbar.php'; ?>
  <div class="login-container">
    <h2>Login to MyDistrictJobs</h2>

    <?php if ($error): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="login-form">
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" required />
      </div>

      <button type="submit" class="btn-submit">Login</button>
    </form>

    <div class="links">
      <a href="password/forgot_password.php">Forgot password?</a>
      <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </div>
<?php include 'footer.php';?>
</body>
</html>
