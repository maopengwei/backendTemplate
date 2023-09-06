<?php
namespace app\admin\logic;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


class Email
{
    public static function index($email,$content)
    {
        $mail = new PHPMailer(true);


        try {
            //Server settings
//            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.163.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = '17513109798@163.com';                     // SMTP username
            $mail->Password   = 'KVQOZNLXATPHQLTA';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 25;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above


            $mail->CharSet = 'UTF-8';
            //Recipients收件人
            $mail->setFrom($mail->Username,'mao');
            $mail->addAddress($email, '用户');     // Add a recipient

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = '主题报告';
            $mail->Body    = $content;
            $mail->AltBody = $content;

            $mail->send();
            $rel = 1;
        } catch (Exception $e) {

            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $rel = 0;

        }
        if($rel){
            dump('success');
        }else{
            dump('error');
        }
    }

}





