<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch the request to ensure it belongs to the user and is approved
    $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = :id AND user_id = :user_id AND status = 'approved'");
    $stmt->execute(['id' => $request_id, 'user_id' => $user_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        // Path to the PDF file
        $filePath = 'generated_docs/request_' . $request_id . '.pdf';

        // Check if the file exists
        if (file_exists($filePath)) {
            // Set the content type to display the PDF in the browser
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
            readfile($filePath);
            exit;
        } else {
            die("File not found.");
        }
    } else {
        die("Invalid request.");
    }
} else {
    die("No request ID provided.");
}
