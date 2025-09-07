<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration.
    |--------------------------------------------------------------------------
    |
    | Paramètres pour le système de cache.
    | Assurez-vous que l’utilisateur Apache a les permissions nécessaires sur le répertoire cache.
    |
    */

    // Activer le SuperCache
    'super_cache' => false,

    // Répertoire de stockage des données de cache
    'data_dir' => BASEPATH .'storage' .DS .'cache' .DS,

    // Mode auto-cleanup : 0 = désactivé, 1 = activé
    'run_cleanup' => 1,

    // Fréquence du nettoyage automatique (1-100)
    'cleanup_freq' => 20,

    // Durée maximale d’un cache en secondes (ici 24 heures)
    'max_age' => 86400,

    // Sauvegarder les statistiques instantanées : 0 = non, 1 = oui
    'save_stats' => 0,

    // Terminer le processus HTTP après avoir envoyé la page cache : 0 = non, 1 = oui
    'exit' => 0,

    // Limite de "webuser" pour que SuperCache ne nettoie pas le cache
    'clean_limit' => 300,

    // Même cache standard pour anonymes et membres : 0 = non, 1 = oui
    'non_differentiate' => 0,

];
