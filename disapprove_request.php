<?php
require 'db_connect.php';
require 'notification_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $rejection_reason = $_POST['disapprove_reason']; // Get the reason from the form

    // Update request status and rejection reason
    $sql = "UPDATE requests SET status = 'rejected', rejection_reason = :rejection_reason WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
    $stmt->bindParam(':rejection_reason', $rejection_reason, PDO::PARAM_STR); // Bind the reason

    if ($stmt->execute()) {
        // Get the user ID associated with the request
        $userSql = "SELECT user_id FROM requests WHERE id = :id";
        $userStmt = $pdo->prepare($userSql);
        $userStmt->bindParam(':id', $request_id, PDO::PARAM_INT);
        $userStmt->execute();
        $request = $userStmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            $userId = $request['user_id'];

            // Send notification to the user about the rejection
            $message = "Your request #$request_id has been rejected. Reason: $rejection_reason";
            sendNotification($userId, $message, $pdo);
        }

        // Redirect back to the dashboard or show a success message
        header("Location: admin_request.php");
        exit;
    } else {
        echo "Error rejecting request.";
    }
}
?>
