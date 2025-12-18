<?php
session_start();
require 'db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $sql = "SELECT id, first_name, last_name, password, role, is_approved, age, province,region,city,barangay ,phone, gender, course,student_no 
                FROM users 
                WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        
        // Bind the email parameter
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "No user found with this email.";
            header("Location: login.php");
            exit();
        }
        
        // Verify the password
        if ($user && $user['password'] == $password) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['first_name'] . " " . $user['last_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['age'] = $user['age'] ?? null;
            $_SESSION['province'] = $user['region'] ?? null;
            $_SESSION['province'] = $user['province'] ?? null;
            $_SESSION['province'] = $user['city'] ?? null;
            $_SESSION['province'] = $user['barangay'] ?? null;
            $_SESSION['phone'] = $user['phone'] ?? null;
            $_SESSION['gender'] = $user['gender'] ?? null;
            $_SESSION['course'] = $user['course'] ?? null;
            $_SESSION['student_no'] = $user['student_no'] ?? null;

            // Check account approval status
            if ($user['is_approved'] === 'pending' || $user['is_approved'] === 'disapproved') {
                $_SESSION['error'] = "Your account is not approved. Please contact an administrator.";
                header("Location: login.php");
                exit();
            }
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                // $_SESSION['success'] = "Login successful! Welcome back, " . $_SESSION['name'] . ".";
                header("Location: dashboard_admin.php");
            } else {
                // $_SESSION['success'] = "Login successful! Welcome back, " . $_SESSION['name'] . ".";
                header("Location: dashboard_user.php");
            }
            exit();
        } else {
            // Set an error message if login fails
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
