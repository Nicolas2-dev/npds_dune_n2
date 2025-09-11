<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Aliases de classes.
    |--------------------------------------------------------------------------
    |
    | Cette section définit les alias pour les classes les plus utilisées
    | dans l'application. Cela permet d'utiliser ces classes via un
    | nom plus court dans tout le code.
    |
    */

    // Liste des alias de classes enregistrés.
    'aliases' => array(
        // Npds
        'Config'   => 'Npds\Config\Config',
        'View'     => 'Npds\View\View',

        'Event'    => 'Npds\Support\Facades\Event',
        'Redirect' => 'Npds\Support\Facades\Redirect',
        'Request'  => 'Npds\Support\Facades\Request',
        'Response' => 'Npds\Support\Facades\Response',
        'Route'    => 'Npds\Support\Facades\Route',

        // App

        'Access'        => 'App\Support\Facades\Access',
        'Assets'        => 'App\Support\Facades\Assets',
        'Css'           => 'App\Support\Facades\Css',
        'Js'            => 'App\Support\Facades\Js',
        'Auth'          => 'App\Support\Facades\Auth',
        'Block'         => 'App\Support\Facades\Block',
        'Chat'          => 'App\Support\Facades\Chat',
        'Code'          => 'App\Support\Facades\Code',
        'Cookie'        => 'App\Support\Facades\Cookie',
        'Sql'           => 'App\Support\Facades\Sql',
        'Date'          => 'App\Support\Facades\Date',
        'Debug'         => 'App\Support\Facades\Debug',
        'Download'      => 'App\Support\Facades\Download',
        'Editeur'       => 'App\Support\Facades\Editeur',
        'Edito'         => 'App\Support\Facades\Edito',
        'Encrypter'     => 'App\Support\Facades\Encrypter',
        'Forum'         => 'App\Support\Facades\Forum',
        'Groupe'        => 'App\Support\Facades\Groupe',
        'Language'      => 'App\Support\Facades\Language',
        'Log'           => 'App\Support\Facades\Log',
        'Mailer'        => 'App\Support\Facades\Mailer',
        'Media'         => 'App\Support\Facades\Media',
        'Smilies'       => 'App\Support\Facades\Smilies',
        'Messenger'     => 'App\Support\Facades\Messenger',
        'Metalang'      => 'App\Support\Facades\Metalang',
        'News'          => 'App\Support\Facades\News',
        'Online'        => 'App\Support\Facades\Online',
        'PageRef'       => 'App\Support\Facades\PageRef',
        'Paginator'     => 'App\Support\Facades\Paginator',
        'Password'      => 'App\Support\Facades\Password',
        'Pollbooth'     => 'App\Support\Facades\Pollbooth',
        'Url'           => 'App\Support\Facades\Url',
        'Session'       => 'App\Support\Facades\Session',
        'Spam'          => 'App\Support\Facades\Spam',
        'Stat'          => 'App\Support\Facades\Stat',
        'Subscribe'     => 'App\Support\Facades\Subscribe',
        'Theme'         => 'App\Support\Facades\Theme',
        'UserMenu'      => 'App\Support\Facades\UserMenu',
        'User'          => 'App\Support\Facades\User',

    ),

];
