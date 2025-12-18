<?php
require 'db_connect.php'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['id'];

    $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = :id");
    $stmt->bindParam(':id', $studentId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not approve student.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
