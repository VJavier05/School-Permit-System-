<?php
session_start();

// Include the email.php file
require 'email.php';
require_once 'db_connect.php'; // Database connection

// Set the time zone to Asia/Manila (or your preferred time zone)
date_default_timezone_set('Asia/Manila'); // You can change this to any time zone as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Basic validation for the email
    if (empty($email)) {
        $_SESSION['error'] = "Please enter your email.";
        header("Location: forgot_password.php");
        exit();
    }

    // Generate a unique token
    $token = bin2hex(random_bytes(16)); // Generates a secure 32-character token (16 bytes)

    // Get the current time in the selected time zone
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expiration time (1 hour from now)
    echo "Expires At: " . $expires_at; // Debugging the expiration time

    try {
        // Store the token in the password_resets table along with the email and expiration time
        $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email, 'token' => $token, 'expires_at' => $expires_at]);

        // Create the reset password link
        $resetLink = "http://localhost/school_system2/reset_password.php?token=" . $token; // Link to reset password

        // Send the email
        $subject = 'Password Reset Request';
        $body = "Click here to reset your password: <a href='$resetLink'>$resetLink</a>";
        $emailSent = sendEmail($email, $subject, $body);

        if ($emailSent) {
          $_SESSION['success'] = "A password reset link has been sent to your email.";
          header("Location: login.php");
          exit();
        } else {
          $_SESSION['error'] = "There was an error sending the email.";
          header("Location: forgot_password.php"); // Redirect to the same page to show the error
          exit();
        }
    } catch (PDOException $e) {
      $_SESSION['error'] = "Error: " . $e->getMessage();
      header("Location: forgot_password.php"); // Redirect to the same page to show the error
      exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="static/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="static/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="static/page-auth.css" class="template-customizer-theme-css" />

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>
<body>

<!-- <form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Reset Link</button>
</form> -->


<div class="container-xxl">

<?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Forgot Password -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="d-flex justify-content-center align-items-center" style="height: 100px;">
                  <span class="app-brand-logo demo">
                      <img src="static/img/school_logo.png" alt="DASHBOARD LOGO" class="img-fluid">
                  </span>
              </div>

              <!-- /Logo -->
              <h4 class="mb-2">Forgot Password? 🔒</h4>
              <p class="mb-4">Enter your email and we'll send you instructions to reset your password</p>
              <form id="formAuthentication" class="mb-3" method="POST">
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="text"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    autofocus
                  />
                </div>
                <button type="submit" class="btn btn-primary d-grid w-100">Send Reset Link</button>
              </form>
              <div class="text-center">
                <a href="login.php" class="d-flex align-items-center justify-content-center">
                  <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                  Back to login
                </a>
              </div>
            </div>
          </div>
          <!-- /Forgot Password -->
        </div>
      </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


</body>
</html>