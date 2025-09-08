<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Configuration.
    |--------------------------------------------------------------------------
    |
    | Paramètres de connexion à la base de données MySQL.
    | 
    | dbhost      : Hôte de la base de données MySQL
    | dbuname     : Nom d'utilisateur MySQL
    | dbpass      : Mot de passe MySQL
    | dbname      : Nom de la base de données
    | mysql_p     : Connexion persistante (true/false)
    | mysql_i     : Utiliser l'extension MySQLi (true)
    | debugmysql  : Activer le log et le rapport d'erreurs pour MySQL
    |
    */

    'dbhost'      => 'localhost',
    'dbuname'     => 'npdslocal',
    'dbpass'      => ']Y!FomJW6rAeFK7!',
    'dbname'      => 'npdslocal',
    'mysql_p'     => true,
    'mysql_i'     => true,
    'debugmysql'  => false,

    /*
    |--------------------------------------------------------------------------
    | Options database.
    |--------------------------------------------------------------------------
    |
    */

    // Détermine la taille maximale pour un fichier dans le processus SaveMysql
    'savemysql_size' => 256,

    // Type de processus MySQL (1, 2 ou 3)
    'savemysql_mode' => 1,

];
