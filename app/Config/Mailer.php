<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fonction Mail.
    |--------------------------------------------------------------------------
    |
    */

    // Email de l’administrateur du site
    'adminmail' => 'webmaster@site.fr',

    // Quelle fonction Mail utiliser (1=mail, 2=email)
    'mail_fonction' => 1,


    /*
    |--------------------------------------------------------------------------
    | PHPMailer Configuration.
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'envoi d'emails via SMTP avec PHPMailer
    |
    */

    'php_mailer' => [

        // Serveur SMTP
        'smtp_host'     => '',

        // Port TCP (587 si TLS activé)
        'smtp_port'     => 587,

        // Activer l'authentification SMTP (true/false)
        'smtp_auth'     => false,

        // Nom d'utilisateur SMTP
        'smtp_username' => '',

        // Mot de passe SMTP
        'smtp_password' => '',

        // Activer le chiffrement TLS (true/false)
        'smtp_secure'   => false,

        // Type de chiffrement TLS ('tls' ou 'ssl')
        'smtp_crypt'    => 'tls',

        // DKIM : 1 = utiliser celui du DNS, 2 = génération automatique
        'dkim_auto'     => 1,
    ],

];
