<?php
session_start();
require 'db_connect.php'; 

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    try {
        $sql = "DELETE FROM notifications WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);


    } catch (PDOException $e) {
        $_SESSION['error'] = "Error clearing notifications: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "You must be logged in to clear notifications.";
}

header("Location: dashboard_user.php"); 
exit();
