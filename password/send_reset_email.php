<?php
// send_reset_email.php
session_start();
include '../includes/db.php'; // ✅ Your database connection

// Include PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
require '../PHPMailer/Exception.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    // 1. Check if the email exists
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // 2. Generate secure token
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        // 3. Store token in DB
        $update = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        // 4. Create reset link
        $resetLink = "http://localhost/job_board/password/reset_password.php?token=$token";

        // 5. Send Email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'kanyikevictory@gmail.com';        
            $mail->Password   = 'llri dxdh cxcc cdat';           
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Email settings
            $mail->setFrom('kanyikevictory@gmail.com', 'LocalLink UG');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "LocalLink UG Password Reset";
            $mail->Body    = "
                <p>Hi,</p>
                <p>We received a password reset request for your LocalLink UG account.</p>
                <p>Click the link below to reset your password:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>This link will expire in 30 minutes.</p>
                <p>If you didn’t request a reset, you can ignore this email.</p>
            ";

            $mail->send();
            echo "✅ A password reset link has been sent to your email.";
        } catch (Exception $e) {
            echo "❌ Email not sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ No account found with that email.";
    }
}
?>
