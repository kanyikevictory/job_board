
<?php
session_start();
include 'includes/db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'];

    // Password validation: min 8 chars, at least 1 letter and 1 number
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d].{8,}$/', $password)) {
        $error = "Password must be at least 8 characters long and contain at least one letter and one number.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }

    if (empty($error)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, telephone, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $telephone, $hashed_password, $role);
            if ($stmt->execute()) {
    // Redirect to login page after successful registration
    header("Location: login.php?registered=1");
    exit();
}
 else {
                $error = "Registration failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MyDistrictJobs</title>
    <link rel="stylesheet" href="css/register.css">  
</head>
<body >
<?php include 'navbar.php'; ?>
    <div class="form-wrapper">
        <div card-form>
        <h2 class="form-title">Create Account</h2>

        <?php if (!empty($success)): ?>
            <div class="alert success-alert">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="alert error-alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="telephone">Telephone Number</label>
                <input type="tel" name="telephone"
                    pattern="^(\+256|0)[0-9]{9}$"
                    title="Must start with +256 or 0 followed by 9 digits"
                    class="form-input"
                    required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                       title="Password must be at least 8 characters long and contain at least one letter and one number"
                       class="form-input"
                       required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password"
                       class="form-input"
                       required>
            </div>

            <div class="form-group">
                <label for="role">Register as:</label>
                <select name="role" class="form-input" required>
                    <option value="job_seeker">Job Seeker</option>
                    <option value="job_poster">Job Poster</option>
                </select>
            </div>

            <button type="submit" class="btn-primary">
                Register
            </button>
        </form>

        <p class="text-center text-sm mt-4">
            Already have an account? 
            <a href="login.php" class="link-primary">Login here</a>
        </p>
        </div>
    </div>
<?php include 'footer.php';?>
</body>
</html>
