<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Options des référents HTTP.
    |--------------------------------------------------------------------------
    |
    */

    // Activer le suivi des référents HTTP pour savoir qui fait un lien vers notre site ? (1=Oui 0=Non)
    'httpref' => 1,

    // Nombre maximum de référents HTTP à stocker dans la base de données (évitez de mettre un nombre trop élevé, 500 ~ 1000 est correct)
    'httprefmax' => 1000,

];
