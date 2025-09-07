<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Fuseau horaire par défaut.
    |--------------------------------------------------------------------------
    |
    | Définissez ici le fuseau horaire par défaut pour votre site.
    | Consultez : http://www.php.net/manual/fr/timezones.php
    |
    */

    // Fuseau horaire par défaut.
    'timezone' => 'Europe/Paris',

    // Configuration locale pour afficher correctement la date selon votre décalage GMT.
    'gmt' => '0',

    /*
    |--------------------------------------------------------------------------
    | Format de date par défaut.
    |--------------------------------------------------------------------------
    */

    // Format de la date (PHP date format).
    'date_format' => 'Y-m-d',

    // Format de l'heure (PHP date format).
    'time_format' => 'H:i:s',

    // Format complet date + heure (PHP date format).
    'datetime_format' => 'Y-m-d H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Horaires du jour et de la nuit.
    |--------------------------------------------------------------------------
    |
    | Définissez l'heure de début du jour et de la nuit pour le site.
    |
    */

    // HH:MM où le jour commence.
    'lever' => '08:00',

    // HH:MM où la nuit commence.
    'coucher' => '20:00',

];
