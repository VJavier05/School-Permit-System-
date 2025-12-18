<?php
session_start();
require 'db_connect.php';
require 'notification_functions.php';

// Check if 'id' and 'action' parameters are set
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    // Determine the status based on the action
    if ($action === 'approve' || $action == 'return_approved') {
        $status = 'approved';
        $message = "Your account has been approved.";
    } elseif ($action === 'disapprove') {
        $status = 'disapproved';
        $message = "Your account has been disapproved.";
    } elseif ($action === 'return') {
        $status = 'pending';
        $message = "Your account status has been returned to pending.";
    }elseif($action == 'archieve'){
        $status = 'archieve';
        $message = "Your account has been archived.";
    }

    try {
        $sql = "UPDATE users SET is_approved = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        // Execute the statement and set a session message
        if ($stmt->execute()) {
            $_SESSION['success'] = "Student status successfully updated to " . ucfirst($status) . ".";

            sendNotification($id, $message, $pdo);
        } else {
            $_SESSION['error'] = "Failed to update the student status.";
        }

           // Redirect based on the action
            if ($action === 'archieve' || $action == 'return_approved') {
                header("Location: user_management.php");
            } else {
                header("Location: pending_account.php");
            }
            exit();


    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    }
}

