<?php

if (! function_exists('topdownload'))
{ 
    #autodoc topdownload() : Bloc topdownload <br />=> syntaxe : function#topdownload
    function topdownload()
    {
        global $block_title;

        $title = $block_title == '' ? translate('Les plus téléchargés') : $block_title;

        $boxstuff = '<ul>';
        $boxstuff .= topdownload_data('short', 'dcounter');
        $boxstuff .= '</ul>';

        if (strpos($boxstuff, '<li') === false) {
            $boxstuff = '';
        }

        themesidebox($title, $boxstuff);
    }
}

if (! function_exists('lastdownload'))
{ 
    #autodoc lastdownload() : Bloc lastdownload <br />=> syntaxe : function#lastdownload
    function lastdownload()
    {
        global $block_title;

        $title = $block_title == '' ? translate('Fichiers les + récents') : $block_title;

        $boxstuff = '<ul>';
        $boxstuff .= topdownload_data('short', 'ddate');
        $boxstuff .= '</ul>';

        if (strpos($boxstuff, '<li') === false) {
            $boxstuff = '';
        }

        themesidebox($title, $boxstuff);
    }
}
