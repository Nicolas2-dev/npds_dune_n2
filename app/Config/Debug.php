<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mode Debug.
    |--------------------------------------------------------------------------
    |
    | Si activé, les erreurs PHP seront affichées directement.
    |
    */

    // Debug interne
    'debug_handler' => true,

    // Active ou désactive le mode debug.
    'debug' => false,

    // type possible : php ou whoops
    'type' => 'php',

    // Niveau d'erreur PHP : 
    // 'none'     => 0,
    // 'dev'      => E_ERROR | E_WARNING | E_PARSE | E_NOTICE,
    // 'standard' => E_ERROR | E_WARNING | E_PARSE,
    // 'all'      => E_ALL,
    'level' => 0,

    // Active le handler PrettyPage si true, sinon simple handler.
    'whoops' => [
        'pretty_page' => false,
    ],

];
