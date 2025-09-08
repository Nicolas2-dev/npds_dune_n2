<?php

use App\Library\Theme\Theme;
use App\Library\Language\Language;

if (! function_exists('bloc_langue')) {
    #autodoc bloc_langue() : Bloc langue <br />=> syntaxe : function#bloc_langue
    function bloc_langue()
    {
        global $block_title, $multi_langue;

        if ($multi_langue) {
            $title = $block_title == '' ? translate('Choisir une langue') : $block_title;

            Theme::themeSidebox($title, Language::affLocalLangue('index.php', 'choice_user_language', ''));
        }
    }
}
