<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proxy Configuration.
    |--------------------------------------------------------------------------
    |
    | Définition des proxys pour différents sites de news identifiés par leur ID
    |
    */

    'proxies' => [
        998 => [
            'url'  => 'proxy-npds.org',
            'port' => 80,
        ],
        999 => [
            'url'  => 'proxy-npds.org',
            'port' => 8080,
        ],
    ],

];
