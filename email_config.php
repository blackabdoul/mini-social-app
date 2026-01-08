<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

function sendEmail($to, $subject, $htmlMessage) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'abdulmaliklawal12345@gmail.com';        
        $mail->Password   = 'ojnnmclqzlhuwfil';   
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Timeout settings (helpful for slow connections)
        $mail->Timeout    = 30;
        $mail->SMTPDebug  = 0; // 0 = off, 2 = detailed debug
        
        // Recipients
        $mail->setFrom('noreply@yoursite.com', 'MiniSocialApp');
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlMessage;
        $mail->AltBody = strip_tags($htmlMessage);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log the error
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
