<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Options par Page.
    |--------------------------------------------------------------------------
    |
    | Chaque page a ses propres réglages de cache :
    | - timing : durée du cache en secondes
    | - query  : expression régulière pour filtrer les Query Strings
    |
    | Exemple :
    |    'index.php' => [
    |        'timing' => 300,   // 5 minutes
    |        'query'  => "^",   // toutes les Query Strings
    |    ],
    |    'leprog.php' => [
    |        'timing' => 300,   // par exemple 5 minutes
    |        'query'  => "^opc=(visite|modification|commentaire)",
    |    ],
    |    'section.php' => [
    |        'timing' => 300,   // par exemple 5 minutes
    |        'query'  => "^offset=(10|20|30)&cat=[0-9]{1,2}",
    |    ],
    |    'news.php' => [
    |        'timing' => 300,   // par exemple 5 minutes
    |        'query'  => "^idn=[0-9]{1,2}",
    |    ],
    |
    */

    'pages' => [
        'index.php' => [
            'timing' => 300,
            'query'  => "^",
        ],
        'article.php' => [
            'timing' => 300,
            'query'  => "^",
        ],
        'sections.php' => [
            'timing' => 300,
            'query'  => "^op",
        ],
        'faq.php' => [
            'timing' => 86400,
            'query'  => "^myfaq",
        ],
        'links.php' => [
            'timing' => 28800,
            'query'  => "^",
        ],
        'forum.php' => [
            'timing' => 3600,
            'query'  => "^",
        ],
        'memberslist.php' => [
            'timing' => 1800,
            'query'  => "^",
        ],
        'modules.php' => [
            'timing' => 3600,
            'query'  => "^",
        ],
    ],

];
