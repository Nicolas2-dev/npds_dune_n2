<?php

return [


    /*
    |--------------------------------------------------------------------------
    | Fonction de parsing.
    |--------------------------------------------------------------------------
    |
    | Choisissez la fonction de parsing que vous souhaitez utiliser par défaut.
    |
    */

    // Sélection de la fonction de parsing préférée
    'parse' => 1,

    // PHP > 5.x : par défaut 0 / PHP < 5.x envoi de HTML compressé avec zlib : 1 - attention
    'gzhandler' => 0,

    /*
    |--------------------------------------------------------------------------
    | URL du site.
    |--------------------------------------------------------------------------
    |
    | L'URL de base de votre site web.
    |
    */

    // URL de l'application
    'url' => 'http://localhost:8080/',
    
    // URL complète de votre site (Ne pas mettre de / à la fin) => obsolète
    //'nuke_url' => 'https://dev.twocms.fr',

    /*
    |--------------------------------------------------------------------------
    | Informations sur le site.
    |--------------------------------------------------------------------------
    |
    | Nom, slogan et logos de votre site.
    |
    */

    // Nom de l'application
    'name' => 'Npds Dev Framework',

    // Nom du site
    'sitename' => 'test dev',

    // Phrase pour le titre du site (balise HTML Title)
    'Titlesitename' => 'dev test',

    // Logo pour les pages imprimables (il est conseillé d'avoir un graphique noir/blanc)
    'site_logo' => 'assets/images/npds/npds_p.gif',

    // Slogan du site
    'slogan' => 'dev test npds',

    /*
    |--------------------------------------------------------------------------
    | Page par défaut et date de démarrage.
    |--------------------------------------------------------------------------
    |
    | Configuration de la page d'accueil et date de début pour les statistiques.
    |
    */

    // Page par défaut de votre site (par défaut : index.php mais vous pouvez utiliser : topics.php, links.php ...)
    //'Start_Page' => 'index.php?op=edito',
    'Start_Page' => 'index/edito',

    // Date de départ à afficher dans la page des statistiques
    'startdate' => '20/08/2025',

    /*
    |--------------------------------------------------------------------------
    | Ne pas toucher aux options suivantes !
    |--------------------------------------------------------------------------
    |
    | Clés internes système.
    |
    */

    // Clé interne NPDS
    'NPDS_Key' => '68a5fd2524344',

];
