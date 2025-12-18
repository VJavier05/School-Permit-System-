<?php
session_start();
require_once 'db_connect.php'; // Include your database connection

// Check if the request method is GET or POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission (only on POST)
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: reset_password.php?token=" . $_GET['token']); // Stay on the reset page
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: reset_password.php?token=" . $_GET['token']); // Stay on the reset page
        exit();
    }

    // Validate token
    if (!isset($_GET['token']) || empty($_GET['token'])) {
        $_SESSION['error'] = "Invalid or missing token.";
        header("Location: forgot_password.php"); // Redirect to forgot password page
        exit();
    }

    $token = $_GET['token'];

    try {
        // Fetch token and email from the password_resets table
        $sql = "SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['token' => $token]);
        $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the token is invalid or expired
        if (!$resetRequest) {
            $_SESSION['error'] = "Invalid or expired reset token.";
            header("Location: forgot_password.php"); // Redirect to forgot password page
            exit();
        }

        // Token is valid, process password reset
        $email = $resetRequest['email'];

        // Hash the new password before storing (optional, depending on your security needs)
        // $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Update the user's password in the users table
        $updateSql = "UPDATE users SET password = :password WHERE email = :email";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute(['password' => $password, 'email' => $email]);

        // Delete the token from the password_resets table
        $deleteSql = "DELETE FROM password_resets WHERE token = :token";
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->execute(['token' => $token]);

        // Success message and redirect to login page
        $_SESSION['success'] = "Your password has been reset successfully.";
        header("Location: login.php"); // Redirect to login
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
        header("Location: reset_password.php?token=" . $_GET['token']); // Stay on the reset page
        exit();
    }
} else {
    // If the request method is GET, show the reset password form
    if (!isset($_GET['token']) || empty($_GET['token'])) {
        $_SESSION['error'] = "Invalid or missing token.";
        header("Location: forgot_password.php"); // Redirect to forgot password page
        exit();
    }
    // If token is valid, show reset password form
    // Otherwise, you can add a redirect to show an error if needed
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="static/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="static/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="static/page-auth.css" class="template-customizer-theme-css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>
<body>

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Reset Your Password</h4>
                    <!-- Display success/error messages -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; ?>
                        </div>
                    <?php unset($_SESSION['error']); endif; ?>
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= $_SESSION['success']; ?>
                        </div>
                    <?php unset($_SESSION['success']); endif; ?>

                    <!-- Form to reset password -->
                    <form id="formAuthentication" method="POST">
                        <div class="mb-3 form-password-toggle">
                            <label for="password" class="form-label">New Password</label>

                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your new password" required />
                                <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility(this)">
                                    <i class="bx bx-hide"></i>
                                </span>    
                            </div>
                        </div>


                        <div class="mb-3 form-password-toggle">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter your new password" required />
                                <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility(this)">
                                        <i class="bx bx-hide"></i>
                                </span> 
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary d-grid w-100">Reset Password</button>
                    </form>
                    <div class="text-center">
                        <a href="login.php" class="d-flex align-items-center justify-content-center">
                            <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                            Back to login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="static/js/password.js"></script>

</body>
</html>
