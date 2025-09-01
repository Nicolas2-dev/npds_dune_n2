<?php

/************************************************************************/
/* SFORM Extender for NPDS USER                                         */
/* ===========================                                          */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/
/* Dont modify this file is you dont know what you make                 */
/************************************************************************/

$sform_path = 'library/sform/';

include_once $sform_path . 'sform.php';

global $m;
$m = new Sform();

$m->addFormTitle('Register');

$m->addFormId('register');

$m->addFormMethod('post');

$m->addFormCheck('false');

$m->addUrl('user.php');

include $sform_path . 'extend-user/mod_formulaire.php';

echo $m->printForm('');
