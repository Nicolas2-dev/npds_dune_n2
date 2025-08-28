<?php


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

use App\Library\Assets\Css;

echo 'coucou je suis npds mvc en cour de dev !';

$tmp = '';

$tmp .= Css::import_css('npds-boost_sk', 'french', 'cerulean');

//$tmp .= Css::import_css_javascript('npds-boost_sk', 'french', 'cerulean');

dump($tmp);