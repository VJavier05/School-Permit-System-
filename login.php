<?php
session_start(); 
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

<div class="container-fluid vh-100">

<?php
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
            . $_SESSION['success'] .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        unset($_SESSION['success']); 
    }


    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
            . $_SESSION['error'] .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        unset($_SESSION['error']); 
    }
    ?>
    <div class="row h-100 start">
        <!-- Left Side (Image) -->
        <div class="col-lg-6 d-none d-lg-block image-side"></div>
        
        <!-- Right Side (Login Form) -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="w-75">
                <h2 class="mb-4 text-center">Welcome Back</h2>
                
                <form action="login_process.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your Email" required>
                    </div>
                    <div class="mb-3">
                        
                    <div class="form-password-toggle">
                    <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                    <a href="forgot_password.php">
                      <small>Forgot Password?</small>
                    </a>
                  </div>
                        <div class="input-group input-group-merge">
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                placeholder="••••••••••••"
                                name="password"
                            />
                            <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility(this)">
                                <i class="bx bx-hide"></i>
                            </span>
                        </div>
                    </div>


                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="text-center mt-3">
                    <a href="register.php">Create an Account</a>
                </p>
            </div>
        </div>
    </div>
</div>



    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


<script src="static/js/password.js"></script>
</body>
</html>