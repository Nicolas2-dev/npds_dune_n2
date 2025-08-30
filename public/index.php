<?php

use Npds\Contracts\Http\Kernel;
use Npds\Http\Request;


/*
|--------------------------------------------------------------------------
| Enregistrement de l'auto-chargement
|--------------------------------------------------------------------------
|
| Composer fournit un chargeur de classes automatique pratique pour
| cette application. Il nous suffit de l'utiliser ! Nous allons simplement
| l'inclure dans ce script afin de ne pas avoir à charger nos classes
| manuellement.
|
*/

require __DIR__.'/../vendor/autoload.php';


/*
|--------------------------------------------------------------------------
| Exécution de l'application
|--------------------------------------------------------------------------
|
| Une fois que nous avons l'application, nous pouvons traiter la requête entrante
| en utilisant le kernel HTTP de l'application. Ensuite, nous renverrons la réponse
| au navigateur du client, lui permettant de profiter de notre application.
|
*/

$app = require_once __DIR__.'/../app/Platform/Bootstrap.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
); //->send();

dump($app, $response);