<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* snipe 2004                                                           */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

use App\Library\Assets\Css;
use App\Library\Media\Smilies;

if (!function_exists('Mysql_Connexion')) {
    include 'mainfile.php';
}

include 'functions.php';

if (isset($user)) {
    if ($cookie[9] == '') {
        $cookie[9] = $Default_Theme;
    }

    if (isset($theme)) {
        $cookie[9] = $theme;
    }

    $tmp_theme = $cookie[9];

    if (!$file = @opendir('themes/' . $cookie[9])) {
        $tmp_theme = $Default_Theme;
    }
} else {
    $tmp_theme = $Default_Theme;
}

include 'storage/meta/meta.php';

echo '<link rel="stylesheet" href="assets/skins/default/bootstrap.min.css">';

echo Css::importCss($tmp_theme, $language, '', '', '');

include 'library/formhelp.java.php';

echo '</head>
    <body class="p-2">
    ' . Smilies::putitemsMore() . '
    </body>
</html>';
