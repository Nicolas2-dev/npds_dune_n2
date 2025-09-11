<?php

use App\Support\Facades\Theme;
use App\Support\Facades\Download;


if (! function_exists('topdownload')) {
    #autodoc topdownload() : Bloc topdownload <br />=> syntaxe : function#topdownload
    function topdownload()
    {
        global $block_title;

        $title = $block_title == '' ? translate('Les plus téléchargés') : $block_title;

        $boxstuff = '<ul>';
        $boxstuff .= Download::topDownloadData('short', 'dcounter');
        $boxstuff .= '</ul>';

        if (strpos($boxstuff, '<li') === false) {
            $boxstuff = '';
        }

        Theme::themeSidebox($title, $boxstuff);
    }
}

if (! function_exists('lastdownload')) {
    #autodoc lastdownload() : Bloc lastdownload <br />=> syntaxe : function#lastdownload
    function lastdownload()
    {
        global $block_title;

        $title = $block_title == '' ? translate('Fichiers les + récents') : $block_title;

        $boxstuff = '<ul>';
        $boxstuff .= Download::topDownloadData('short', 'ddate');
        $boxstuff .= '</ul>';

        if (strpos($boxstuff, '<li') === false) {
            $boxstuff = '';
        }

        Theme::themeSidebox($title, $boxstuff);
    }
}
