<?php 

//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;

//require 'src/Exception.php';
require 'src/PHPMailer.php';
//require 'src/SMTP.php';

$mail = new PHPMailer(true);



try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = $SMTPHOST;                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = $SMTPUSERNAME;                     // SMTP username
    $mail->Password   = $SMTPPASSWORD;                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; 
    $mail->Port       = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom($NOREPLYEMAIL, 'Mailer');
    $mail->addAddress($RECIVEREMAIL, 'Joe User');
    //$mail->addReplyTo($NOREPLYEMAIL, 'Information');

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

?>