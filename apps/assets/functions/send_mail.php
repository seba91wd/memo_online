<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_mail($mail_info){

    require './apps/assets/PHPMailer/src/Exception.php';
    require './apps/assets/PHPMailer/src/PHPMailer.php';
    require './apps/assets/PHPMailer/src/SMTP.php';

    //Load Composer's autoloader
    // require 'vendor/autoload.php';

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    $user = 'validation@memo_online.com';
    $password = '*********************';

    try {
        //Server settings
        // Mode debug, a desactiver apres la fin des tests
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        // Paramétre de type SMTP
        $mail->isSMTP();

        //Adresse IP ou DNS du serveur SMTP
        $mail->Host = 'smtp.orange.fr';

        // Utiliser l'identification
        $mail->SMTPAuth = true;   

        // Adresse email à utiliser
        $mail->Username = $user;

        //Mot de passe de l'adresse email à utiliser
        $mail->Password = $password;

        // Protocole de sécurisation des échanges avec le SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        // Port TCP du serveur SMTP
        $mail->Port = 465;

        // Mail d'envoi (le nom est optionel)
        $mail->setFrom($user, 'Mémo Online');

        // Mail destinataire (le nom est optionel)
        $mail->addAddress($mail_info['user_mail']);

        // $mail->addAddress('ellen@example.com');               
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');

        // Mail de la copie cachée
        // $mail->addBCC('admin@memo_online.fr', 'Copie caché');

        
        // Contenu du mail
        
        // Contenu au format HTML
        $mail->isHTML(true); 
        
        // Pièce jointe
        // $mail->addAttachment('/var/tmp/file.tar.gz');
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');

        $mail->Subject = $mail_info['mail_subject'];
        $mail->Body = $mail_info['mail_body'];

        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if ($mail->send()){
            return true;
        }

    } catch (Exception $e) {
        echo "Le message n'a pas été envoyé. Erreur : {$mail->ErrorInfo}";
        return false;
    }
};
