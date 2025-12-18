<?php
function sendAdminNotification($message, $pdo) {
    $admin_id = 6; 

    $notification_sql = "INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)";
    $notification_stmt = $pdo->prepare($notification_sql);
    $notification_stmt->execute(['user_id' => $admin_id, 'message' => $message]);
}
?>