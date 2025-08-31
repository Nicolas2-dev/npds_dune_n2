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

require __DIR__ . '/vendor/autoload.php';

include 'bootstrap/grab_globals.php';
include 'config/config.php';
include 'bootstrap/multi-langue.php';
include 'language/' . $language . '/lang-' . $language . '.php';

include 'library/supercache/SuperCacheManager.php';
include 'library/supercache/SuperCacheEmpty.php';
include 'library/supercache/cache.php';
include_once 'config/cache.config.php';
include_once 'config/cache.timings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'shared/PHPMailer/src/Exception.php';
require 'shared/PHPMailer/src/PHPMailer.php';
require 'shared/PHPMailer/src/SMTP.php';

include 'library/database/mysqli.php';
include 'library/meta-lang/adv-meta_lang.php';

$dblink = Mysql_Connexion();

$mainfile = 1;

require_once 'auth.inc.php';

if (isset($user)) {
    $cookie = cookieDecode($user);
}

session_manage();

$tab_langue = make_tab_langue();

global $meta_glossaire;
$meta_glossaire = charg_metalang();

date_default_timezone_set('Europe/Paris');

// charegement des blocks.
loadBlocks('blocks');
