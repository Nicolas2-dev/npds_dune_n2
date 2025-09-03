<?php

/************************************************************************/
/* SFORM Extender for NPDS V Forum .                                    */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/
/* Dont modify this file is you dont know what you make                 */
/************************************************************************/

use App\Library\Sform\Sform;

$sform_path = 'library/sform/';

global $m;
$m = new Sform();

$m->addFormTitle('Bugs_Report');

$m->addFormMethod('post');

$m->addFormCheck('false');

$m->addMess(' * d&eacute;signe un champ obligatoire ');

$m->addSubmitValue('submitS');

$m->addUrl('newtopic.php');

include $sform_path . 'forum/' . $formulaire;

if (isset($submitS)) {
    $message = $m->affResponse('', 'not_echo', '');
} else {
    echo $m->printForm('');
}
