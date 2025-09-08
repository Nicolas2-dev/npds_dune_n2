<?php

use Npds\Http\Request;
use Npds\Config\Config;
use Npds\Http\Response;
use Npds\Routing\Router;
use Npds\Application\AliasLoader;
use App\Exceptions\Handler as ExceptionHandler;

/*
|--------------------------------------------------------------------------
| Définition du séparateur de dossier (DS).
|--------------------------------------------------------------------------
*/

/**
 * Séparateur de dossier compatible avec tous les systèmes.
 * Exemple : "/" sous Linux, "\" sous Windows.
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/*
|--------------------------------------------------------------------------
| Définition des chemins principaux du projet.
|--------------------------------------------------------------------------
*/

/**
 * Chemin racine du projet.
 *
 * @var string
 */
define('BASEPATH', realpath(__DIR__ . '/../') . DS);

/**
 * Chemin du répertoire web/public.
 *
 * @var string
 */
define('WEBPATH', realpath(__DIR__) . DS);

/**
 * Chemin du répertoire app (code source principal).
 *
 * @var string
 */
define('APPPATH', BASEPATH . 'app' . DS);

/*
|--------------------------------------------------------------------------
| Chargement de l’autoloader Composer.
|--------------------------------------------------------------------------
|
| Permet le chargement automatique des classes via PSR-4.
|
*/
require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Initialisation des erreurs PHP.
|--------------------------------------------------------------------------
*/
error_reporting(-1);
ini_set('display_errors', 'Off');

/*
|--------------------------------------------------------------------------
| Chargement des fichiers de configuration.
|--------------------------------------------------------------------------
*/
require APPPATH . 'Config.php';

// Parcours et chargement dynamique des fichiers de configuration
foreach (glob(APPPATH . 'Config/*.php') as $path) {
    $key = lcfirst(pathinfo($path, PATHINFO_FILENAME));
    Config::set($key, require($path));
}

/*
|--------------------------------------------------------------------------
| Définition du fuseau horaire par défaut.
|--------------------------------------------------------------------------
*/
$timezone = Config::get('app.timezone', 'Europe/London');
date_default_timezone_set($timezone);

/*
|--------------------------------------------------------------------------
| Initialisation du gestionnaire d’exceptions.
|--------------------------------------------------------------------------
*/
ExceptionHandler::initialize();

/*
|--------------------------------------------------------------------------
| Initialisation du chargeur d’alias.
|--------------------------------------------------------------------------
*/
AliasLoader::initialize();

/*
|--------------------------------------------------------------------------
| Exécution du bootstrap local.
|--------------------------------------------------------------------------
*/
require APPPATH . 'Bootstrap' . DS . 'Bootstrap.php';

/*
|--------------------------------------------------------------------------
| Initialisation du routeur et chargement des routes.
|--------------------------------------------------------------------------
*/
$router = Router::getInstance();
require APPPATH . 'Routes' . DS . 'Front' . DS . 'Routes.php';
require APPPATH . 'Routes' . DS . 'Admin' . DS . 'Routes.php';

/*
|--------------------------------------------------------------------------
| Récupération de la requête HTTP.
|--------------------------------------------------------------------------
*/
$request = Request::getInstance();

/*
|--------------------------------------------------------------------------
| Dispatch de la requête et génération de la réponse.
|--------------------------------------------------------------------------
*/
$response = $router->dispatch($request);

if (! $response instanceof Response) {
    $response = new Response($response);
}

// Envoi de la réponse HTTP
$response->send();


dump(
    asset_url('css/style.css', 'theme::base'),
    asset_url('css/style.css', 'module::geoloc'),
);