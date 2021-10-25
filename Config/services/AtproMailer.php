<?php

namespace Atpro\mvc\Config\services;


use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class AtproMailer
{
    /**
     * @param $from {{ mail du destinataire }}
     * @param $companyName {{ nom de le  compagnie }}
     * @param array $recipients {{ le ou les recepteurs }}
     * @param null $replyTo {{ réponse à un autre que l'expéditeur (le nom est facultatif) }}
     * @param null $cc {{ (copie) : autant d'adresse que souhaité  ( facultatif)}}
     * @param null $bcc {{  (Copie cachée) :  : autant d'adresse que souhaité ( facultatif) }}
     * @param null $attachments {{  Pièces jointes en gardant le nom du fichier sur le serveur (facultatif) }}
     * @param $subject {{ Objet du message  }}
     * @param $body {{ corps du message }}
     * @return bool
     */
    public static function sendMail(
        $from,
        $companyName,
        array $recipients,
        $replyTo = null,
        $cc = null,
        $bcc = null,
        $attachments = null,
        string $subject = null,
        string $body
    ): bool
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0;//2;           // Enable verbose debug output
            $mail->isSMTP();                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;               // Enable SMTP authentication
            $mail->Username = 'atpro029@gmail.com';   // SMTP username
            $mail->Password = 'atpro126';             // SMTP password
            $mail->SMTPSecure = 'ssl'; //'tls';          // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;  //587;              // TCP port to connect to
            $mail->Charset = 'utf-8';       // encoding

            /**
             * Recipients
             */
            $mail->setFrom($from, $companyName);
            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient);
            }

            if ($replyTo !== null) {
                $mail->addReplyTo($replyTo, 'Information');
            }

            if ($cc !== null) {
                if (is_array($cc) && count($cc) !== 0) {
                    foreach ($cc as $carboncopy) {
                        $mail->addCC($carboncopy);
                    }
                }
                if (!is_array($cc)) {
                    $mail->addCC($cc);
                }
            }

            if ($bcc !== null) {
                if (is_array($bcc) && count($bcc) !== 0) {
                    foreach ($bcc as $blindcarboncopy) {
                        $mail->addBCC($blindcarboncopy);
                    }
                }
                if (!is_array($bcc)) {
                    $mail->addBCC($bcc);
                }
            }

            /**
             * Attachments
             */
            if ($attachments !== null) {
                if (is_array($attachments) && count($attachments) !== 0) {
                    foreach ($attachments as $attachment) {
                        $mail->addAttachment($attachment);
                    }
                }
                if (!is_array($attachments)) {
                    $mail->addAttachment($attachments);
                }
            }

            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = 'Default text mail!';

            return $mail->send();
        } catch (Exception) {
            echo "Message non envoyer. Erreur: $mail->ErrorInfo";
            return false;
        }
    }
}
