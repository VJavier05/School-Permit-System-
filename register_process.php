<?php
session_start();
require 'db_connect.php'; 
require 'adminnotification.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user input
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $mobile = trim($_POST['mobile']);
    $course = trim($_POST['course']);
    $region = trim($_POST['region']);
    $province = trim($_POST['province']);
    $city = trim($_POST['city']);
    $barangay = trim($_POST['barangay']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $year = trim($_POST['year']);
    $section = trim($_POST['section']);
    $s_number = trim($_POST['s_number']);

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php"); // Redirect back to the registration page
        exit();
    }

    // Check if the email is unique
    $check_email_sql = "SELECT COUNT(*) FROM users WHERE email = :email";
    $stmt = $pdo->prepare($check_email_sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error'] = "Email already exists.";
        header("Location: register.php");
        exit();
    }

    // Hash the password for security
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement to prevent SQL injection
    $sql = "INSERT INTO users 
    (first_name, last_name, gender, age, phone, course, region, province, city, barangay, email, password, role, year_level, section, student_no) 
    VALUES 
    (:first_name, :last_name, :gender, :age, :phone, :course, :region, :province, :city, :barangay, :email, :password, 'user', :year, :section, :s_number)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':gender' => $gender,
            ':age' => $age,
            ':phone' => $mobile,
            ':course' => $course,
            ':region' => $region,
            ':province' => $province,
            ':city' => $city,
            ':barangay' => $barangay,
            ':email' => $email,
            ':password' => $password,
            ':year' => $year,
            ':section' => $section,
            ':s_number' => $s_number
        ]);

        $message = "A new user, $first_name $last_name, has registered.";
        sendAdminNotification($message, $pdo);

        $_SESSION['success'] = "Registration successful!";
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>
