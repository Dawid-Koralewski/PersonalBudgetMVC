<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Mail
 * 
 * PHP version 8.2.4
 */


 class Mail
 {
    /**
     * Send a message
     * 
     * @param string $to Recipent
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     * 
     * @return mixed
     */

     public static function send($to, $subject, $htmlEmailContent)
     {
        $mail = new PHPMailer(true);
        // Composing and sending message
        try
        {
            $mail->isSMTP();
            $mail->Host = Config::MAILER_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = Config::MAILER_USERNAME;
            $mail->Password = Config::MAILER_PASSWORD;
            $mail->SMTPSecure = Config::MAILER_SMTP_SECURE;
            $mail->Port = Config::MAILER_PORT;
            $mail->SMTPDebug = Config::MAILER_SMTP_DEBUG;

            $mail->setFrom('dasiokk@gmail.com');
            $mail->addAddress($to);
            $mail->addReplyTo('dasiokk@gmail.com');
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlEmailContent;

            $mail->send();
        }
        catch (Exception $e)
        {
            echo 'Message not sent: ', $mail->ErrorInfo;
        }
     }
 }