<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $id = $_POST['id'];
    $firstName = $_POST['first_name']; // First name
    $lastName = $_POST['last_name']; // Last name
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $course = $_POST['course'];
    $region = $_POST['region']; // Region
    $province = $_POST['province']; // Province
    $city = $_POST['city']; // City
    $barangay = $_POST['barangay']; // Barangay

    try {
        // Update the student's information
        $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, age = :age, gender = :gender, 
                phone = :phone, course = :course, region = :region, province = :province, city = :city, barangay = :barangay 
                WHERE id = :id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':age' => $age,
            ':gender' => $gender,
            ':phone' => $phone,
            ':course' => $course,
            ':region' => $region,
            ':province' => $province,
            ':city' => $city,
            ':barangay' => $barangay
        ]);

        $_SESSION['success'] = "Student information updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    }
}

header("Location: user_management.php");
exit();
?>
