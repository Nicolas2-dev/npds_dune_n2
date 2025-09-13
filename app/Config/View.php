<?php

return [

    /*
    |--------------------------------------------------------------- -------------------------
    | Afficher les chemins de stockage
    |--------------------------------------------------------------- -------------------------
    |
    | La plupart des systèmes de création de modèles chargent les modèles à partir du disque. Ici, vous 
    | pouvez spécifier
    | un tableau de chemins qui doivent être vérifiés pour vos vues. Bien sûr
    | le chemin de vue Framework habituel a déjà été enregistré pour vous.
    |
    */

    'paths' => [
        APPPATH .'Views'
    ],

    /*
    |--------------------------------------------------------------- -------------------------
    | Chemin de vue compilé
    |--------------------------------------------------------------- -------------------------
    |
    | Cette option détermine où tous les fichiers modèles compilés seront
    | stocké pour votre application. En règle générale, cela se trouve dans le stockage
    | annuaire. Cependant, comme d'habitude, vous êtes libre de modifier cette valeur.
    |
    */

    'compiled' => STORAGE_PATH .'framework' .DS .'views',

];
