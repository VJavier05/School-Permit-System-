<?php 
session_start();
require 'db_connect.php';
require 'notification_functions.php';
require 'fpdf/fpdf.php'; // Include FPDF library

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];

    // Update the request status to 'approved'
    $sql = "UPDATE requests SET status = 'approved' WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $request_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Retrieve user and request details
        $userSql = "SELECT r.user_id, r.request_type, CONCAT(u.first_name, ' ', u.last_name) AS name, u.email, u.course 
            FROM requests r
            JOIN users u ON r.user_id = u.id
            WHERE r.id = :id";
        $userStmt = $pdo->prepare($userSql);
        $userStmt->bindParam(':id', $request_id, PDO::PARAM_INT);
        $userStmt->execute();
        $request = $userStmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            $userId = $request['user_id'];
            $userName = $request['name'];
            $userEmail = $request['email'];
            $requestType = $request['request_type'];
            $userCourse = $request['course'];
            $timestamp = $request['timestamp'];
         

            // Generate PDF document for the request
            $pdf = new FPDF();
            $pdf->AddPage();

            // Add University Header with Logo
            //$pdf->Image('university_logo.png', 10, 10, 20); // Adjust logo placement
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 5, 'Republic of the Philippines', 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 7, 'Laguna State Polytechnic University', 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 5, 'Main Campus, Sta. Cruz, Laguna', 0, 1, 'C');
            $pdf->Cell(0, 5, 'Tel. No. (049) 304-7000', 0, 1, 'C');
            $pdf->Cell(0, 5, 'E-mail: lspu_scc_reg@yahoo.com', 0, 1, 'C');
            $pdf->Ln(10);

            // Add Certification Title based on the request type
            $pdf->Cell(0, 9, 'OFFICE OF THE UNIVERSITY REGISTRAR', 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 16);
            if ($requestType == "COR") {
                $pdf->Cell(0, 10, 'CERTIFICATE OF REGISTRATION', 0, 1, 'C');
            } elseif ($requestType == "COE") {
                $pdf->Cell(0, 10, 'CERTIFICATE OF ENROLLMENT', 0, 1, 'C');
            } elseif ($requestType == "DIPLOMA") {
                $pdf->Cell(0, 10, 'CERTIFICATE OF GRADUATION', 0, 1, 'C');
            } elseif ($requestType == "TOR") {
                $pdf->Cell(0, 10, 'TRANSCRIPT OF RECORD', 0, 1, 'C');
            } elseif ($requestType == "COG") {
                $pdf->Cell(0, 10, 'CERTIFICATE OF GRADE', 0, 1, 'C');
            } else {
                // Default if request_type doesn't match any known type
                $pdf->Cell(0, 10, 'CERTIFICATE', 0, 1, 'C');
            }
            $pdf->Ln(10);


            // Add Certification Body Text
            $pdf->SetFont('Arial', '', 12);
            if ($requestType == "COE") {
                $pdf->MultiCell(0, 10, 
                    'To Whom It May Concern:' . "\n\n" .
                    'This is to certify that MS/MR. ' . $userName . ' is enrolled in the degree of ' . $userCourse . ', this First Semester Academic Year 2024-2025.' . 
                    "\n\n" . 
                    'Issued upon request of MS/MR. ' . $userName . ' for whatever purpose it may serve them.'
                );
            } elseif ($requestType == "DIPLOMA") {
                $pdf->MultiCell(0, 10, 
                    'This is to certify that ' . $userName . ' has successfully completed all the required coursework and has met all the academic requirements of the ' . $userCourse . ' at Laguna State Polytechnic University.' . 
                    "\n\n" . 
                    'The student has demonstrated proficiency and has satisfactorily completed the program in accordance with the rules and regulations set forth by the institution.' . 
                    "\n\n" . 
                    $userName . ' has been awarded the degree of ' . $userCourse . ', and is considered a graduate of 2025-2026.' . 
                    "\n\n" . 
                    'We wish ' . $userName . ' the best in all future endeavors.'
                );
            } elseif ($requestType == "COR") {
               
                $pdf->SetFont('Arial', 'B', 12);

                $pdf->SetX(10); 
                $pdf->MultiCell(0, 10, 
                    'First (1st) Semester, A.Y. 2024-2025', 0, 'C'); 

                $pdf->SetFont('Arial', '', 12); // Adjust font as needed

                $pdf->SetFont('Arial', '', 12);

               
                $pdf->Cell(60, 10, 'Student Name  :  ' . $userName, 0, 0); 
                $pdf->Cell(0, 10, 'Student No.  :  ' . $userId, 0, 1, 'R'); 

                $pdf->Cell(60, 10, 'Course        :  ' . $userCourse, 0, 0); 
                $pdf->Cell(0, 10, 'Year Level   :  Third Year', 0, 1, 'R'); 

                $pdf->Cell(60, 10, 'Date Enrolled :  Aug 14, 2024', 0, 0); 
                $pdf->Cell(0, 10, 'Date Request :  ' . $timestamp, 0, 1, 'R'); 

                // Subject Information
                $pdf->Ln(5); 
                $pdf->SetFont('Arial', 'B', 12); // Bold for the heading
                $pdf->Cell(0, 10, 'Subject Information:', 0, 1); // Full width for the heading

                
                // Add Table for subjects
                $pdf->SetFont('Arial', 'B', 6);
                $pdf->Cell(20, 10, 'Subject Code', 1, 0, 'C');
                $pdf->Cell(45, 10, 'Subject Title', 1, 0, 'C');
                $pdf->Cell(20, 10, 'Section', 1, 0, 'C');
                $pdf->Cell(15, 10, 'Unit', 1, 0, 'C');
                $pdf->Cell(30, 10, 'Time/Day', 1, 0, 'C');
                $pdf->Cell(25, 10, 'Room', 1, 0, 'C');
                $pdf->Cell(30, 10, 'Professor', 1, 1, 'C');

                // Add Subjects Table Content (Replace this with actual query results)
                $subjects = [
                    ['ITEP 308', 'System Integration and Architecture 1', 'BSIT-3D-WAM', '3', '03:00 PM - 05:00 PM / Mon', 'ROOM 106', 'Maria Laureen B. Miranda'],
                    ['', '', 'BSIT-3D-WAM', '', '02:00 PM - 05:00 PM / Wed', 'ROOM 203 (COM. LAB)', 'Gener Mosico'],
                    ['ITEP 309', 'Networking 2', 'BSIT-3D-WAM', '3', '07:00 AM - 10:00 AM / Wed', 'ROOM 202 (COM. LAB)', 'Reymart Joseph Pielago'],
                    ['ITEP 310', 'Social and Professional Issues', 'BSIT-3D-WAM', '3', '07:00 AM - 10:00 AM / Tue', 'ROOM 102', 'Kaellah Lansang'],
                    ['ITEL 304', 'Integrative Programming Technologies 2', 'BSIT-3D-WAM', '3', '10:00 AM - 01:00 PM / Thu', 'ROOM 206 (COM. LAB)', 'Romel Serrano'],
                    ['', '', 'BSIT-3D-WAM', '', '03:00 PM - 05:00 PM / Thu', 'ROOM 206 (COM. LAB)', 'Romel Serrano'],
                    ['ITST 301', 'Principles of Web Design (WMA 301)', 'BSIT-3D-WAM', '3', '07:00 AM - 10:00 AM / Mon', 'ROOM 203 (COM. LAB)', 'Edward S. Flores'],
                    ['', '', '', '', '10:00 AM - 12:00 PM / Mon', 'ROOM 107', 'Edward S. Flores'],
                    ['ITST 302', 'Client-Server Tenologies (WMA 302)', 'BSIT-3D-WAM', '3', '07:00 AM - 09:00 AM / Thu', 'ROOM 105 (Mini. Lib.)', 'Princess Joy Tuazon'],
                    ['', '', '', '', '10:00 AM - 01:00 PM / Fri', 'ROOM 206 (COM. LAB)', 'Princess Joy Tuazon'],
                ];
                

                foreach ($subjects as $subject) {
                    $pdf->SetFont('Arial', '', 6);
                    $pdf->Cell(20, 10, $subject[0], 1, 0, 'C');
                    $pdf->Cell(45, 10, $subject[1], 1, 0, 'C');
                    $pdf->Cell(20, 10, $subject[2], 1, 0, 'C');
                    $pdf->Cell(15, 10, $subject[3], 1, 0, 'C');
                    $pdf->Cell(30, 10, $subject[4], 1, 0, 'C');
                    $pdf->Cell(25, 10, $subject[5], 1, 0, 'C');
                    $pdf->Cell(30, 10, $subject[6], 1, 1, 'C');
                }
            }

            $pdf->Ln(20);

            // Add Authorized Signature
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 5, '_________________________', 0, 1, 'R');
            $pdf->Cell(0, 5, 'TEDDY G. YUN', 0, 1, 'R');
            $pdf->Cell(0, 5, 'Registrar III', 0, 1, 'R');
            $pdf->Ln(10);
            

            // Save PDF file to directory
            $outputDir = 'generated_docs/';
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0777, true); // Create directory if it doesn't exist
            }
            $fileName = $outputDir . "request_" . $request_id . ".pdf";
            $pdf->Output('F', $fileName);

            // Notify user about the approval and provide a link to the PDF
            $message = "Your request #$request_id has been approved";
            sendNotification($userId, $message, $pdo);
        }

        // Redirect back to the admin request page
        header("Location: admin_request.php");
        $_SESSION['success'] = "request approval Done.";

        exit;
    } else {
        // Store the error in the session and redirect back
        $_SESSION['error'] = "Error processing request approval.";
        header("Location: admin_request.php");
        exit;
    }
}
?>
