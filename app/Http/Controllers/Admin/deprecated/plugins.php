<?php

/************************************************************************/
/* DUNE by NPDS - admin prototype                                       */
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

use App\Library\Url\Url;

if (!function_exists('admindroits')) {
    include 'die.php';
}

include 'header.php';

if ($ModPath != '') {

    $isControllerAdmin = (strpos($ModPath, 'admin') !== false) || (strpos($ModStart, 'admin') !== false);

    if ($isControllerAdmin) {
        $$ModPath = preg_replace('#^admin/#', '', $ModPath);
        $ModStart = preg_replace('#^admin/#', '', $ModStart);
    }

    $controllerPath = $isControllerAdmin
        ? 'modules/' . $ModPath . '/http/controllers/admin/' . $ModStart . '.php'
        : 'modules/' . $ModPath . '/http/controllers/front/' . $ModStart . '.php';

    if (file_exists($controllerPath)) {
        include $controllerPath;
    }
} else {
    Url::redirectUrl(urldecode($ModStart));
}
