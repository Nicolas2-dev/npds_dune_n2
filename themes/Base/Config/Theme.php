<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration.
    |--------------------------------------------------------------------------
    |
    */

    // Nom du thème (un suffix _sk permet l'utilisation des skins)
    'name' => 'Base',

    //'light' ou 'dark' ou 'auto' 
    // light  : (utilise les classes défaut de bootstrap)
    // dark : (utilise les classes dark de bootstrap)
    // auto : (utilise automatiquement et alternativement (light/dark) les classes de bootsrap en fonction 
    // de la configuration de l'application|système)
    'theme_darkness' => 'auto',

    // Nombre de caractères affichés avant troncature pour certains blocs
    'long_chain' => 34,

    // Définition des balises conteneur global utilisées dans l'en-tête et le pied de page
    'ContainerGlobal' => [
        'header' => '<div id="container">', // Ouverture du conteneur principal autour du corps de la page
        'footer' => '</div>',               // Fermeture du conteneur principal autour du corps de la page
    ],

    // Classes CSS supplémentaires pour les colonnes gauche et droite
    'moreclass' => [
        'right' => 'col-12', // Classe appliquée aux blocs de droite (par défaut pleine largeur)
        'left'  => 'col',    // Classe appliquée aux blocs de gauche (par défaut largeur automatique)
    ],
    
];
