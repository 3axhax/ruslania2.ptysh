<?php /** Created by Кирилл rkv@dfaktor.ru 21.08.2019 20:40*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once Yii::getPathOfAlias('webroot') . '/PHPMailer/src/Exception.php';
require_once Yii::getPathOfAlias('webroot') . '/PHPMailer/src/PHPMailer.php';
require_once Yii::getPathOfAlias('webroot') . '/PHPMailer/src/SMTP.php';

class MailSender {

    function __construct() {

    }

    function send() {
        $mail = new PHPMailer;
//Tell PHPMailer to use SMTP
        $mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
        $mail->SMTPDebug = 2;
//Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
        $mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = "rkv@dfaktor.ru";
//Password to use for SMTP authentication
        $mail->Password = "";
//Set who the message is to be sent from
        $mail->setFrom('noreply@ruslania.com', 'First Last');
//Set an alternative reply-to address
//        $mail->addReplyTo('kirill.ruh@gmail.com', 'First Last');
//Set who the message is to be sent to
        $mail->addAddress('kirill.ruh@gmail.com', 'kirill');
//Set the subject line
        $mail->Subject = 'PHPMailer GMail SMTP test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//        $mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        $mail->msgHTML('PHPMailer GMail SMTP test');

//Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//        $mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
            //Section 2: IMAP
            //Uncomment these to save your message in the 'Sent Mail' folder.
            #if (save_mail($mail)) {
            #    echo "Message saved!";
            #}
        }
    }

}