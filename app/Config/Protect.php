<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Protection des URLs.
    |--------------------------------------------------------------------------
    |
    | Liste des mots-clés ou motifs à filtrer pour sécuriser l'application
    | contre les injections SQL, XSS et autres attaques web courantes.
    |
    */

    'filters' => [
        // Filtrage des scripts et worm PHP
        'perl',
        'chr(',

        // Prévention des injections SQL
        ' union ',
        ' into ',
        ' select ',
        ' update ',
        ' from ',
        ' where ',
        ' insert ',
        ' drop ',
        ' delete ',
        '/*', // Commentaire SQL inline

        // Prévention des attaques XSS
        'outfile',
        '/script',
        'url(',
        '/object',
        'img dynsrc',
        'img lowsrc',
        '/applet',
        '/style',
        '/iframe',
        '/frameset',
        'document.cookie',
        'document.location',
        'msgbox(',
        'alert(',
        'expression(',

        // Quelques attributs HTML5 sensibles
        'formaction',
        'autofocus',
        'onforminput',
        'onformchange',
        'history.pushstate(',
    ],

];
