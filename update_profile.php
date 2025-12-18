<?php
session_start(); // Start the session for handling messages

// Database connection
require_once 'db_connect.php'; // Include your database connection file

// Initialize variables for error and success messages
$error_message = '';
$success_message = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $region = $_POST['region'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $barangay = $_POST['barangay'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $course = $_POST['course'];
    $email = $_POST['email'];
    $student_no = $_POST['stundet_no']; // Note: Ensure this matches correctly

    // Initialize profile picture path
    $profile_picture_path = null;

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($file['type'], $allowed_types)) {
            $error_message = "Invalid file type. Only JPG and PNG are allowed.";
        } else {
            // Validate file size (max 2MB)
            $max_file_size = 2 * 1024 * 1024; // 2MB
            if ($file['size'] > $max_file_size) {
                $error_message = "File size exceeds 2MB.";
            } else {
                // Create upload directory if it doesn't exist
                $upload_dir = 'uploads/profile_pictures/';
                if (!file_exists($upload_dir)) {
                    if (!mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) {
                        $error_message = "Failed to create upload directory.";
                    }
                }

                // Generate unique filename
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $file_name = uniqid('profile_', true) . '.' . $file_extension;
                $profile_picture_path = $upload_dir . $file_name;

                // Move uploaded file
                if (!move_uploaded_file($file['tmp_name'], $profile_picture_path)) {
                    $error_message = "Failed to upload profile picture.";
                }
            }
        }
    }

    // If there are no errors, proceed to update the database
    if (!$error_message) {
        try {
            // Update query
            $sql = "
                UPDATE users
                SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    age = :age,
                    region = :region,
                    province = :province,
                    city = :city,
                    barangay = :barangay,
                    phone = :phone,
                    gender = :gender,
                    course = :course,
                    email = :email";

            // Include profile picture if uploaded
            if ($profile_picture_path) {
                $sql .= ", profile_picture = :profile_picture";
            }

            $sql .= " WHERE student_no = :student_no";

            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':region', $region);
            $stmt->bindParam(':province', $province);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':barangay', $barangay);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':course', $course);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':student_no', $student_no);

            if ($profile_picture_path) {
                $stmt->bindParam(':profile_picture', $profile_picture_path);
            }

            $stmt->execute();

            $success_message = "Profile updated successfully.";
        } catch (PDOException $e) {
            $error_message = "Failed to update profile: " . $e->getMessage();
        }
    }

    // Set session messages
    if ($error_message) {
        $_SESSION['error'] = $error_message;
    } else {
        $_SESSION['success'] = $success_message;
    }

    // Redirect to profile page
    header("Location: user_profile.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: user_profile.php");
    exit();
}
