<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notification des Soumissions de News.
    |--------------------------------------------------------------------------
    |
    */

    // Recevoir une notification à chaque nouvelle soumission sur le site ? (1=Oui, 0=Non)
    'notify' => 1,

    // Adresse email pour envoyer la notification
    'notify_email' => 'webmaster@site.fr',

    // Sujet de l’email
    'notify_subject' => 'Nouvelle soumission',

    // Contenu du message de l’email
    'notify_message' => 'Le site a recu une nouvelle soumission !',

    // Nom du compte à afficher dans le champ "From" de l’email
    'notify_from' => 'webmaster@site.fr',

];
