<?php



function sendNotification($userId, $message, $pdo) {
    $sql = "INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId, 'message' => $message]);
}


?>