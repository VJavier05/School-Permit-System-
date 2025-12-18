<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Load environment variables
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

loadEnv(__DIR__ . '/.env');

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
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port       = $_ENV['SMTP_PORT'];

        // Recipients
        $mail->setFrom($senderEmail, $senderName);
        $mail->addAddress($_ENV['RECIPIENT_EMAIL']);
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
