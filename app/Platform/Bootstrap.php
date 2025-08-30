<?php

/*
|--------------------------------------------------------------------------
| Création de l'application
|--------------------------------------------------------------------------
|
| La première étape consiste à créer une nouvelle instance de l'application Laravel,
| qui sert de "colle" pour tous les composants de Laravel, et qui est le conteneur
| IoC du système, liant toutes les différentes parties entre elles.
|
*/

$app = new Npds\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Liaison des interfaces importantes
|--------------------------------------------------------------------------
|
| Ensuite, nous devons lier certaines interfaces importantes dans le conteneur
| afin de pouvoir les résoudre lorsque cela sera nécessaire. Les kernels
| gèrent les requêtes entrantes vers cette application, à la fois pour le web et la CLI.
|
*/

$app->singleton(
    Npds\Contracts\Http\Kernel::class,
    App\Platform\Http\Kernel::class
);




$app->singleton(
    Npds\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);


return $app;