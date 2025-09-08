<?php

//Route::get('/',        'App\Http\Controllers\Front\Home@index');


Route::get('/', 'App\Http\Controllers\Front\Start\StartPage@index');


/**
 * Afficher les informations PHP.
 */
Route::get('phpinfo', function ()
{
    ob_start();

    phpinfo();

    return Response::make(ob_get_clean(), 200);
});


// A revoir !
Route::get('language/{choice_user_language}', array('uses' => function ($choice_user_language)
{
    //return htmlentities($choice_user_language);

    // Note a revoir non finaliser !!!
    /*if (isset($choice_user_language)) {
        if ($choice_user_language != '') {
            
            $user_cook_duration = max(1, Config::get('cookie.user_cook_duration'));

            $timeX = time() + (3600 * $user_cook_duration);

            $languageslist = Language::languageCache();

            if ((stristr($languageslist, $choice_user_language)) and ($choice_user_language != ' ')) {
                setcookie('user_language', $choice_user_language, $timeX);

                // voir pour faire un set user_language dans l'app ! 
                $user_language = $choice_user_language;
            }
        }
    }

    if (Config::get('language.multi_langue')) {
        if (($user_language != '') and ($user_language != " ")) {
            $tmpML = stristr($languageslist, $user_language);
            $tmpML = explode(' ', $tmpML);

            if ($tmpML[0]) {

                // voir pour faire un set language dans l'app ! 
                $language = $tmpML[0];
            }
        }
    }*/ 

    //return $choice_user_language;
    return Redirect::to('/');
    
}, 'where' => array('choice_user_language' => '[a-z]{2}')));

