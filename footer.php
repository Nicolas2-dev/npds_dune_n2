<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

if (!function_exists('Mysql_Connexion')) {
    include 'mainfile.php';
}

function footmsg()
{
    global $foot1, $foot2, $foot3, $foot4;

    $foot = '<p align="center">';

    // Boucle sur les variables $foot1 à $foot4
    for ($i = 1; $i <= 4; $i++) {
        $varName = 'foot' . $i;
        if (!empty($$varName)) {
            $foot .= stripslashes($$varName);
            if ($i < 4) {
                $foot .= '<br />';
            }
        }
    }

    $foot .= '</p>';

    echo aff_langue($foot);
}

function foot()
{
    global $user, $Default_Theme, $cookie9;

    if ($user) {
        $cookie = explode(':', base64_decode($user));

        if ($cookie[9] == '') {
            $cookie[9] = $Default_Theme;
        }

        $ibix = explode('+', urldecode($cookie[9]));

        if (!@opendir('themes/' . $ibix[0])) {
            $theme = $Default_Theme;
        } else {
            $theme = $ibix[0];
        }
    } else {
        $theme = $Default_Theme;
    }

    include 'themes/' . $theme . '/views/footer.php';

    if ($user) {
        $cookie9 = $ibix[0];
    }
}

function footer_after($theme)
{
    if (file_exists($path_theme = 'themes/' . $theme . '/bootstrap/footer_after.php')) {
        include $path_theme;
    } else {
        if (file_exists($path_module = 'themes/base/bootstrap/footer_after.php')) {
            include $path_module;
        }
    }
}

function footer_before()
{
    if (file_exists($path_module = 'themes/base/bootstrap/footer_before.php')) {
        include $path_module;
    }
}

global $tiny_mce;
if ($tiny_mce) {
    echo aff_editeur('tiny_mce', 'end');
}

// include externe file from modules/include for functions, codes ...
footer_before();

foot();

// include externe file from modules/themes include for functions, codes ...
if (isset($user)) {
    global $cookie9;
    footer_after($cookie9);
} else {
    global $Default_Theme;
    footer_after($Default_Theme);
}

echo '
   </body>
</html>';

include 'sitemap.php';

sql_close();
