<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
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

include 'header.php';

$cache_obj = $SuperCache ? new cacheManager() : new SuperCacheEmpty();

if (($SuperCache) and (!$user)) {
    $cache_obj->startCachingPage();
}

if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache) or ($user)) {
    $inclusion = false;
    if (file_exists($path = 'themes/' . $theme . '/html/top.html')) {
        $inclusion = $path;

    } elseif (file_exists($path = 'themes/default/html/top.html')) {
        $inclusion = $path;
    } else {
        echo 'html/top.html / not find !<br />';
    }

    if ($inclusion) {
        ob_start();
            include $inclusion;
            $Xcontent = ob_get_contents();
        ob_end_clean();

        echo meta_lang(aff_langue($Xcontent));
    }
}

// -- SuperCache
if (($SuperCache) and (!$user)) {
    $cache_obj->endCachingPage();
}

include 'footer.php';
