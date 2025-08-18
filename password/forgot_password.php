<!-- forgot_password.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow p-4">
          <h3 class="text-center mb-4">🔐 Forgot Your Password?</h3>
          <form action="send_reset_email.php" method="POST">
            <div class="mb-3">
              <label for="email" class="form-label">Enter your email</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
          </form>
          <p class="text-center mt-3"><a href="login.php">🔙 Back to Login</a></p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
