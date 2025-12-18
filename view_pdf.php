<?php
// Include the database connection
include('db_connect.php');  // Adjust the path if necessary

// Get the request ID from the query string
$requestId = $_GET['request_id'];

// Prepare and execute a query to fetch the PDF path from the database
$stmt = $pdo->prepare("SELECT pdf_path FROM requests WHERE id = :id");
$stmt->bindParam(':id', $requestId);
$stmt->execute();
$pdfPath = $stmt->fetchColumn();

// Ensure the PDF exists before trying to display it
if ($pdfPath && file_exists($pdfPath)) {
    // Display the PDF in the browser using an iframe
    echo '<iframe src="' . htmlspecialchars($pdfPath) . '" width="100%" height="600px"></iframe>';
} else {
    echo "PDF not found.";
}
?>
