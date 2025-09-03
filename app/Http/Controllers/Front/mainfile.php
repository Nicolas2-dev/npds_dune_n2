<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2025 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

use App\Library\Block\Block;
use App\Library\Cookie\Cookie;
use App\Library\Session\Session;
use App\Library\Language\Language;
use App\Library\Metalang\Metalang;

require __DIR__ . '/vendor/autoload.php';

include 'bootstrap/grab_globals.php';
include 'config/config.php';
include 'bootstrap/multi-langue.php';
include 'language/' . $language . '/lang-' . $language . '.php';


include_once 'config/cache.config.php';
include_once 'config/cache.timings.php';

include 'library/database/mysqli.php';

$dblink = Mysql_Connexion();

$mainfile = 1;

require_once 'auth.inc.php';

if (isset($user)) {
    $cookie = Cookie::cookieDecode($user);
}

Session::sessionManage();

$tab_langue = Language::makeTabLangue();

global $meta_glossaire;
$meta_glossaire = Metalang::chargMetalang();

date_default_timezone_set('Europe/Paris');

// charegement des blocks.
Block::loadBlocks('blocks');
