<?php
session_start();
require_once 'db_connect.php';
$current_page = basename($_SERVER['PHP_SELF']);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to view your profile.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch latest data from the database, including profile_picture
$sql = "
SELECT 
    first_name, 
    last_name, 
    age, 
    region, 
    province, 
    city, 
    barangay, 
    phone, 
    gender, 
    course,
    email,
    student_no,
    profile_picture
FROM users 
WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Assign database values to variables for display
    $userFirstName = $user['first_name'];
    $userLastName = $user['last_name'];
    $userAge = $user['age'];
    $userRegion = $user['region'];
    $userProvince = $user['province'];
    $userCity = $user['city'];
    $userBarangay = $user['barangay'];
    $userPhone = $user['phone'];
    $userGender = $user['gender'];
    $userCourse = $user['course'];
    $useremail = $user['email'];
    $userstudent_no = $user['student_no'];
    $userProfilePicture = $user['profile_picture']; // Add profile_picture
} else {
    $_SESSION['error'] = "Unable to retrieve profile information.";
    header("Location: login.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="static/core.css" />
    <link rel="stylesheet" href="static/theme-default.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
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

        <div class="layout-container">
          <!-- Menu -->
          <?php include 'user_template/user_sidebar.php'; ?>

          <!-- Layout container -->
          <div class="layout-page">
            <!-- Navbar -->
  

  
            <!-- / Navbar -->
  
            <!-- Content wrapper -->
            <div class="content-wrapper">
              <!-- Content -->
  
              <div class="container-xxl flex-grow-1 container-p-y">
                
                <div class="card p-4">
                    <h5 class="card-header">Profile Details</h5>
                    
                    <div class="card-body">
                        
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        
                    <img
                        src="<?php echo isset($userProfilePicture) && !empty($userProfilePicture) ? htmlspecialchars($userProfilePicture) : 'static/img/default_pic.png'; ?>"
                        alt="user-avatar"
                        class="d-block rounded"
                        height="100"
                        width="100"
                        id="uploadedAvatar"
                    />

                    <form action="update_profile.php" method="POST" enctype="multipart/form-data">

                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                            </label>
                            <input type="file" name="profile_picture" id="upload" accept="image/png, image/jpeg" style="display: none;">
                            <p class="text-muted mb-0">Allowed JPG or PNG</p>
                        </div>
                    
                    </div>

                    </div>

                    <hr class="my-0" />
                    <div class="card-body">
                    

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($userFirstName); ?>" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($userLastName); ?>" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="age" class="form-label">Age</label>
                                    <input type="text" name="age" class="form-control" value="<?php echo htmlspecialchars($userAge); ?>" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="region" class="form-label">Region</label>
                                    <input type="text" name="region" class="form-control" value="<?php echo htmlspecialchars($userRegion); ?>" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($useremail); ?>" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="stundet_no" class="form-label">Stundet No.</label>
                                    <input type="text" name="stundet_no" class="form-control" value="<?php echo htmlspecialchars($userstudent_no); ?>" required readonly>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="province" class="form-label">Province</label>
                                    <input type="text" name="province" class="form-control" value="<?php echo htmlspecialchars($userProvince); ?>" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($userCity); ?>" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="barangay" class="form-label">Barangay</label>
                                    <input type="text" name="barangay" class="form-control" value="<?php echo htmlspecialchars($userBarangay); ?>" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="phone" class="form-label">Phone No.</label>
                                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($userPhone); ?>" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="" disabled>Select gender</option>
                                        <option value="Male" <?php echo ($userGender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($userGender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo ($userGender == 'Other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="course" class="form-label">Course</label>
                                    <input type="text" class="form-control" id="course" name="course" value="<?php echo htmlspecialchars($userCourse); ?>" required>
                                </div>

                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-primary me-2">Submit</button>
                            </div>
                        </form>


                    </div>

                
          
              </div>
                </div>

              </div>
           
  
  
              <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
          </div>
          <!-- / Layout page -->
        </div>
  
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
      </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>