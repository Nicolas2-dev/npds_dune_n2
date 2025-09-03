<?php

/**
 * Définit le séparateur de dossier (DS) pour améliorer la portabilité du code.
 * 
 * Exemple : "/" sous Linux, "\" sous Windows.
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/**
 * Définit la constante BASEPATH qui pointe vers le répertoire racine du projet.
 */
define('BASEPATH', realpath(__DIR__ . '/../') . DS);

/**
 * Charge l’autoloader de Composer, qui permet de gérer automatiquement
 * le chargement des classes.
 */
require __DIR__ . '/../vendor/autoload.php';

/**
 * Initialise l’application en important le fichier Bootstrap.
 *
 * @var \App\Bootstrap\Bootstrap $npds Instance principale de l’application NPDS.
 */
$npds = require_once 'app' . DS . 'Bootstrap' . DS . 'Bootstrap.php';

/**
 * Débogage dev : affiche un message de confirmation ainsi que l’instance NPDS
 * et le chemin de base de l’application.
 */
dump(
    'hello npds mvc run', 
    $npds,
    $npds->basePath()
);