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

require __DIR__ . '/vendor/autoload.php';

include 'bootstrap/grab_globals.php';

function Access_Error()
{
    include 'admin/die.php';
}

function filtre_module($strtmp)
{
    if (
        strstr($strtmp, '..')
        || stristr($strtmp, 'script')
        || stristr($strtmp, 'cookie')
        || stristr($strtmp, 'iframe')
        || stristr($strtmp, 'applet')
        || stristr($strtmp, 'object')
    ) {
        Access_Error();
    } else {
        return $strtmp != '' ? true : false;
    }
}

if (filtre_module($ModPath) and filtre_module($ModStart)) {
    if (!function_exists('Mysql_Connexion')) {
        include 'mainfile.php';
    }

    $isControllerAdmin = (strpos($ModPath, 'admin') !== false);

    if ($isControllerAdmin) {
        $pos = strpos($ModPath, '/admin');
        $ModPath = substr($ModPath, 0, $pos);
    }

    $controllerPath = $isControllerAdmin
        ? 'modules/' . $ModPath . '/http/controllers/admin/' . $ModStart . '.php'
        : 'modules/' . $ModPath . '/http/controllers/front/' . $ModStart . '.php';

    if (file_exists($controllerPath)) {
        include $controllerPath;
        exit;
    }

    Access_Error();

} else {
    Access_Error();
}
