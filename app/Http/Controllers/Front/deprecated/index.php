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

use App\Library\Log\Log;
use App\Library\String\Sanitize;
use App\Library\auth\Auth;
use App\Library\News\News;
use App\Library\Edito\Edito;
use App\Library\Subscribe\Subscribe;
use App\Library\Cache\SuperCacheEmpty;
use App\Library\Cache\SuperCacheManager;

// Modification pour IZ-Xinstall - EBH - JPB & PHR
if (file_exists('IZ-Xinstall.ok')) {
    if (file_exists('install.php') || is_dir('install')) {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title>NPDS IZ-Xinstall - Installation Configuration</title>
            </head>
            <body>
                <div style="text-align: center; font-size: 20px; font-family: Arial; font-weight: bold; color: #000000"><br />
                    NPDS IZ-Xinstall - Installation &amp; Configuration
                </div>
                <div style="text-align: center; font-size: 20px; font-family: Arial; font-weight: bold; color: #ff0000"><br />
                    Vous devez supprimer le r&eacute;pertoire "install" ET le fichier "install.php" avant de poursuivre !<br />
                    You must remove the directory "install" as well as the file "install.php" before continuing!
                </div>
            </body>
        </html>';
        die();
    }
} else {
    if (file_exists('install.php') && is_dir('install')) {
        header('location: install.php');
    }
}

if (!function_exists('Mysql_Connexion')) {
    include 'mainfile.php';
}

// Redirect for default Start Page of the portal - look at Admin Preferences for choice
function select_start_page($op)
{
    global $Start_Page, $index;

    if (!Auth::autoReg()) {
        global $user;
        unset($user);
    }

    if (($Start_Page == '')
        || ($op == 'index.php')
        || ($op == 'edito')
        || ($op == 'edito-nonews')
    ) {
        $index = 1;

        theindex($op, '', '');
        die();
    } else {
        Header('Location: ' . $Start_Page);
    }
}

function theindex($op, $catid, $marqeur)
{
    include 'header.php';

    // Include cache manager
    global $SuperCache;
    if ($SuperCache) {
        $cache_obj = new SuperCacheManager();
        $cache_obj->startCachingPage();
    } else {
        $cache_obj = new SuperCacheEmpty();
    }

    if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {

        // Appel de la publication de News et la purge automatique
        automatednews();

        global $theme;
        if (($op == 'newcategory')
            || ($op == 'newtopic')
            || ($op == 'newindex')
            || ($op == 'edito-newindex')
        ) {
            aff_news($op, $catid, $marqeur);
        } else {
            if (file_exists('themes/' . $theme . '/views/central.php')) {
                include 'themes/' . $theme . '/Views/central.php';
            } else {
                if (($op == 'edito') || ($op == 'edito-nonews')) {
                    aff_edito();
                }

                if ($op != 'edito-nonews') {
                    aff_news($op, $catid, $marqeur);
                }
            }
        }
    }

    if ($SuperCache) {
        $cache_obj->endCachingPage();
    }

    include 'footer.php';
}

settype($op, 'string');
settype($catid, 'integer');
settype($marqeur, 'integer');

switch ($op) {

    case 'newindex':
    case 'edito-newindex':
    case 'newcategory':
        theindex($op, $catid, $marqeur);
        break;

    case 'newtopic':
        theindex($op, $topic, $marqeur);
        break;

    default:
        select_start_page($op, '');
        break;
}
