<?php

if (! function_exists('userblock'))
{   
    #autodoc userblock() : Bloc membre <br />=> syntaxe : function#userblock
    function userblock()
    {
        global $user, $cookie;

        if (($user) and ($cookie[8])) {
            $ublock = Q_select("SELECT ublock 
                                FROM " . sql_prefix('users') . " 
                                WHERE uid='$cookie[0]'", 86400)[0];

            global $block_title;
            $title = $block_title == '' ? translate('Menu de') . ' ' . $cookie[1] : $block_title;

            themesidebox($title, $ublock['ublock']);
        }
    }
}
