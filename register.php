<?php
session_start();
require 'db_connect.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="static/core.css" />
    <link rel="stylesheet" href="static/theme-default.css" />
    <link rel="stylesheet" href="static/page-auth.css" />

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="container-fluid vh-100">
<?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
            . $_SESSION['error'] .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        unset($_SESSION['error']); // Unset after displaying
    }
    ?>
    <div class="row h-100 start">
        <!-- Left Side (Image) -->
        <div class="col-lg-6 d-none d-lg-block image-side"></div>
        
        <!-- Right Side (Register Form) -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="w-75">
                <h2 class="mb-4 text-center">Create an Account</h2>
                <form action="register_process.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter your First Name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter your Last Name" required>
                        </div>

                 
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="" disabled selected>Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" class="form-control" id="age" name="age" placeholder="Enter your age" required>
                        </div>
                    </div>

                    <div class="mb-3">
                    <label for="mobile" class="form-label">Mobile No.</label>
                    <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="Enter your mobile number" required>
                    </div>


                    <div class="mb-3">
                    <label for="s_number" class="form-label">Stundent No.</label>
                    <input type="text" class="form-control" id="s_number" name="s_number" placeholder="Enter your Stundent No." required>
                    </div>

   

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="course" class="form-label">Course</label>
                            <input type="text" class="form-control" id="course" name="course" placeholder="Example (BSIT, BSCS, BSCPE...)" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="year" class="form-label">Year Level</label>
                            <select class="form-select" id="year" name="year" required>
                                <option value="" disabled selected>Select Year Level</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>

                            </select>                        
                        </div>
                    </div>

                    <div class="mb-3">
                    <label for="section" class="form-label">Section</label>
                    <select class="form-select" id="section" name="section" required>
                                <option value="" disabled selected>Select Section</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>

                            </select>                    
                        </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                        <label for="region" class="form-label">Region</label>
                            <input type="text" class="form-control" id="region" name="region" placeholder="Enter Region" required>
                            </div>
                        <div class="col-md-6 mb-3">
                        <label for="province" class="form-label">Province</label>
                            <input type="text" class="form-control" id="province" name="province" placeholder="Enter Province" required>
                            </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="Enter City" required>
                            </div>
                        <div class="col-md-6 mb-3">
                        <label for="barangay" class="form-label">Barangay</label>
                            <input type="text" class="form-control" id="barangay" name="barangay" placeholder="Enter Barangay" required>
                            </div>
                    </div>

                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="sample@email.com" required>
                    </div>
                
                    <div class="row">
                    <div class="col-md-6 mb-4 form-password-toggle">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="••••••••••"
                                aria-describedby="password"
                                required
                            />
                            <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility(this)">
                                <i class="bx bx-hide"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="col-md-6 mb-4 form-password-toggle">
                        <label class="form-label" for="confirm_password">Confirm Password</label>
                        <div class="input-group input-group-merge">
                            <input
                                type="password"
                                class="form-control"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="••••••••••"
                                aria-describedby="confirm_password"
                                required
                            />
                            <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility(this)">
                                <i class="bx bx-hide"></i>
                            </span>
                        </div>
                    </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                <p class="text-center mt-3">
                    <a href="login.php">Already have an account? Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="static/js/password.js"></script>

</body>
</html>
