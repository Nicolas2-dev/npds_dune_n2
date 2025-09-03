<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* DYNAMIC THEME engine for NPDS                                        */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

global $theme, $Start_Page;

$rep = false;

$Start_Page = str_replace('/', '', $Start_Page);

settype($ContainerGlobal, 'string');

if (file_exists('themes/' . $theme . '/views/partials/header/header.php')) {
    $rep = $theme;
} elseif (file_exists('themes/base/views/partials/header/header.php')) {
    $rep = 'base';
} else {
    echo 'header.php manquant / not find !<br />';
    die();
}

if ($rep) {
    if (file_exists('themes/base/bootstrap/body_onload.php') || file_exists('themes/' . $theme . '/bootstrap/body_onload.php')) {
        $onload_init = ' onload="init();"';
    } else {
        $onload_init = '';
    }

    if (!$ContainerGlobal) {
        echo '<body' . $onload_init . ' class="body" data-bs-theme="' . $theme_darkness . '">';
    } else {
        echo '<body' . $onload_init . ' data-bs-theme="' . $theme_darkness . '">';
        echo $ContainerGlobal;
    }

    ob_start();
    // landing page
    if (stristr($_SERVER['REQUEST_URI'], $Start_Page) && file_exists('themes/' . $rep . '/views/partials/header/header_landing.php')) {
        include 'themes/' . $rep . '/views/partials/header/header_landing.php';
    } else {
        include 'themes/' . $rep . '/views/partials/header/header.php';
    }

    $Xcontent = ob_get_contents();
    ob_end_clean();

    echo Metalang::metaLang(Language::affLangue($Xcontent));
}
