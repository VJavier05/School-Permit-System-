<?php
session_start();
require_once 'db_connect.php'; 
require 'adminnotification.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['formType'], $_POST['yearRange'])) {
        $requestType = $_POST['formType'];
        $academicYear = $_POST['yearRange'];
        $purpose = isset($_POST['purpose']) ? trim($_POST['purpose']) : null;

        // Assuming you have user ID stored in the session
        $userId = $_SESSION['user_id']; 

        
        // Check if there is already a pending request for the same document type
        $checkStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM requests 
        WHERE user_id = ? AND request_type = ? AND academic_year = ? AND status = 'pending'
            ");
        $checkStmt->execute([$userId, $requestType, $academicYear]);
        $ongoingRequestCount = $checkStmt->fetchColumn();

        if ($ongoingRequestCount > 0) {
            // If there is an ongoing request, show an error message
            $_SESSION['error'] = "You already have a pending request for this document type.";
            header("Location: requests_user.php");
            exit();
        }


        // Prepare the SQL statement to prevent SQL injection
        $stmt = $pdo->prepare("
            INSERT INTO requests (user_id, request_type, academic_year, purpose, status) 
            VALUES (?, ?, ?, ?, 'pending')
        ");
        
        // Bind parameters and execute
        $stmt->execute([$userId, $requestType, $academicYear, $purpose]);

        // Check for successful insertion
        if ($stmt->rowCount() > 0) {
            $message = "Request submitted by user ID $userId: $requestType for $academicYear.";
            sendAdminNotification($message, $pdo);

            $_SESSION['success'] = "Request submitted successfully!";
        } else {
            $_SESSION['error'] = "Error submitting request.";
        }

        // Redirect back to the form page or any other page
        header("Location: requests_user.php");
        exit();
    } else {
        $_SESSION['error'] = "Required fields are missing.";
    }
}
?>
