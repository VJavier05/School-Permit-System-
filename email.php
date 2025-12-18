<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function sendEmail($to, $subject, $body, $from = 'your_email@gmail.com', $fromName = 'School System') {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'meeooowe7@gmail.com'; // Your email address
        $mail->Password   = 'ribsazbhwiqujpdl';   // Your app password (create an app password in Gmail settings)
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom($from, $fromName); // Sender's email and name
        $mail->addAddress($to);           // Recipient's email address
        $mail->addReplyTo($from, $fromName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Send email
        $mail->send();
        return true; // Email sent successfully
    } catch (Exception $e) {
        // Error sending email
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false; // Email sending failed
    }
}

?>
