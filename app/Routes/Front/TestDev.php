<?php

// Route Test Dev

/**
 * Home Page Dev test
 */
Route::get(
    'test',        
    'App\Http\Controllers\DevTest\Home@index'
);

/**
 * Afficher les informations PHP.
 */
Route::get('phpinfo', function ()
{
    if (Config::get('debug.debug_handler') === true) {
        ob_start();

        phpinfo();

        return Response::make(ob_get_clean(), 200);
    }

    return Redirect::to('/');
});
