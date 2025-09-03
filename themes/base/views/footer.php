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

global $theme;

$rep = false;

settype($ContainerGlobal, 'string');

if (file_exists('themes/' . $theme . '/views/partials/footer/footer.php')) {
    $rep = $theme;
} elseif (file_exists('themes/base/partials/footer/footer.php')) {
    $rep = 'base';
} else {
    echo 'footer.php manquant / not find !<br />';
    die();
}

if ($rep) {
    ob_start();
    include 'themes/' . $rep . '/views/partials/footer/footer.php';
    $Xcontent = ob_get_contents();
    ob_end_clean();

    if ($ContainerGlobal) {
        $Xcontent .= $ContainerGlobal;
    }

    echo Metalang::metaLang(Language::affLangue($Xcontent));
}
