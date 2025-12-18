<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST["send"])) {
    $mail = new PHPMailer(true);

    try {
        // Sanitize inputs
        $senderEmail = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $senderName = htmlspecialchars($_POST["name"]);
        $subject = htmlspecialchars($_POST["subject"]);
        $message = htmlspecialchars($_POST["message"]);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'meeooowe7@gmail.com';
        $mail->Password   = 'ribsazbhwiqujpdl'; // App Password
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom($senderEmail, $senderName);
        $mail->addAddress('meeooowe7@gmail.com'); // Your email
        $mail->addReplyTo($senderEmail, $senderName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send email
        $mail->send();
        echo "
        <script> 
         alert('Message was sent successfully!');
         document.location.href = 'index.php';
        </script>";
    } catch (Exception $e) {
        echo "
        <script> 
         alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');
        </script>";
    }
}
?>
