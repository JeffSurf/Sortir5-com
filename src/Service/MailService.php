<?php

namespace App\Service;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailService
{

    public function sendmailNoreplyNoTemplate(string $sujet, string $destinataire, string $message, array $fichiers = null): void
    {

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = 'smtp-relay.brevo.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = 'vincent4971@gmail.com';                     //SMTP username
            $mail->Password = 'BPwtbTJvVdUEyq46';
            $mail->CharSet = 'UTF-8';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Expéditeur
            $mail->setFrom('contact@noreply.fr', 'Bob Le Bricoleur');

            // Attachez les fichiers téléchargés à l'e-mail
            if ($fichiers != null) {
                foreach ($fichiers as $fichier) {
                    $mail->addAttachment($fichier->getPathname(), $fichier->getClientOriginalName());
                }
            }

            //Content
            $mail->isHTML(true);
            $mail->Subject = $sujet;
            $mail->Body = $message;

            if ($_ENV['APP_ENV'] == "prod") {
                $mail->addAddress($destinataire);
                $mail->send();
            } else {
                $mail->addAddress("a49.piron@gmail.com");
                $mail->send();
            }

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

}