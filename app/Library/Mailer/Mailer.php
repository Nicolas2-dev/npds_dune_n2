<?php

namespace App\Library\Mailer;

use Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;


class Mailer
{

    /**
     * Envoie un mail en texte ou HTML, avec ou sans pièce jointe.
     *
     * @param string $email     Destinataire
     * @param string $subject   Sujet du mail
     * @param string $message   Contenu du mail
     * @param string $from      Expéditeur (optionnel)
     * @param bool   $priority  Priorité haute si true
     * @param string $mime      'text', 'html', 'html-nobr', 'mixed'
     * @param string|array|null $file Chemin de la pièce jointe ou tableau ['file'=>..,'name'=>..]
     *
     * @return bool True si mail envoyé, false sinon
     */
    public static function send_email(
        string $email,
        string $subject,
        string $message,
        string $from = '',
        bool $priority = false,
        string $mime = 'text',
        string|array|null $file = null
    ): bool {
        global $mail_fonction, $adminmail, $sitename, $NPDS_Key, $nuke_url;

        $From_email = $from != '' ? $from : $adminmail;

        if (preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $From_email)) {

            include 'config/PHPmailer.conf.php';

            if ($dkim_auto == 2) {

                //Private key filename for this selector 
                $privatekeyfile = 'storage/mailer/' . $NPDS_Key . '_dkim_private.pem';

                //Public key filename for this selector 
                $publickeyfile = 'storage/mailer/' . $NPDS_Key . '_dkim_public.pem';

                if (!file_exists($privatekeyfile)) {
                    //Create a 2048-bit RSA key with an SHA256 digest 
                    $pk = openssl_pkey_new(
                        [
                            'digest_alg' => 'sha256',
                            'private_key_bits' => 2048,
                            'private_key_type' => OPENSSL_KEYTYPE_RSA,
                        ]
                    );

                    //Save private key 
                    openssl_pkey_export_to_file($pk, $privatekeyfile);

                    //Save public key 
                    $pubKey = openssl_pkey_get_details($pk);
                    $publickey = $pubKey['key'];

                    file_put_contents($publickeyfile, $publickey);
                }
            }

            $debug = false;
            $mail = new PHPMailer($debug);

            try {
                //Server settings config smtp 
                if ($mail_fonction == 2) {
                    $mail->isSMTP();
                    $mail->Host       = $smtp_host;
                    $mail->SMTPAuth   = $smtp_auth;
                    $mail->Username   = $smtp_username;
                    $mail->Password   = $smtp_password;

                    if ($smtp_secure) {
                        if ($smtp_crypt === 'tls') {
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        } elseif ($smtp_crypt === 'ssl') {
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        }
                    }

                    $mail->Port       = $smtp_port;
                }

                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';

                if ($priority) {
                    $mail->Priority = 2;
                }

                //Recipients 
                $mail->setFrom($adminmail, $sitename);
                $mail->addAddress($email, $email);

                //Content 
                if ($mime == 'mixed') {
                    $mail->isHTML(true);

                    // pièce(s) jointe(s)) 
                    if (!is_null($file)) {
                        if (is_array($file)) {
                            $mail->addAttachment($file['file'], $file['name']);
                        } else {
                            $mail->addAttachment($file);
                        }
                    }
                }

                if (($mime == 'html') or ($mime == 'html-nobr')) {
                    $mail->isHTML(true);

                    if ($mime != 'html-nobr') {
                        $message = nl2br($message);
                    }
                }

                $mail->Subject = $subject;
                $stub_mail = "<html>\n<head>\n<style type='text/css'>\nbody {\nbackground: #FFFFFF;\nfont-family: Tahoma, Calibri, Arial;\nfont-size: 1 rem;\ncolor: #000000;\n}\na, a:visited, a:link, a:hover {\ntext-decoration: underline;\n}\n</style>\n</head>\n<body>\n %s \n</body>\n</html>";

                if ($mime == 'text') {
                    $mail->isHTML(false);
                    $mail->Body = $message;
                } else {
                    $mail->Body = sprintf($stub_mail, $message);
                }

                if ($dkim_auto == 2) {
                    $mail->DKIM_domain = str_replace(['http://', 'https://'], ['', ''], $nuke_url);
                    $mail->DKIM_private = $privatekeyfile;;
                    $mail->DKIM_selector = $NPDS_Key;
                    $mail->DKIM_identity = $mail->From;
                }

                if ($mail_fonction == 2) {
                    if ($debug) {
                        // on génère un journal détaillé après l'envoi du mail 
                        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    }
                }

                $mail->send();

                if ($debug) {
                    // stop l'exécution du script pour affichage du journal sur la page 
                    die();
                }

                $result = true;
            } catch (Exception $e) {
                Ecr_Log('smtpmail', "send Smtp mail by $email", "Message could not be sent. Mailer Error: $mail->ErrorInfo");

                $result = false;
            }
        }

        return $result ? true : false;
    }

    /**
     * Copie un sujet + message dans l'email d'un utilisateur identifié par son ID.
     *
     * @param int    $to_userid ID de l'utilisateur destinataire
     * @param string $sujet     Sujet du mail
     * @param string $message   Contenu du mail (HTML)
     *
     * @return void
     */
    public static function copy_to_email(int $to_userid, string $sujet, string $message): void
    {
        $result = sql_query("SELECT email, send_email 
                            FROM " . sql_prefix('users') . " 
                            WHERE uid='$to_userid'");

        list($mail, $avertir_mail) = sql_fetch_row($result);

        if (($mail) and ($avertir_mail == 1)) {
            static::send_email($mail, $sujet, $message, '', true, 'html', '');
        }
    }
    
}
