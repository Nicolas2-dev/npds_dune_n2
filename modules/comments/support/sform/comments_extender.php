<?php

/************************************************************************/
/* SFORM Extender for Dune comments.                                    */
/* ===========================                                          */
/*                                                                      */
/* P. Brunier 2002 - 2024                                               */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/
/* Dont modify this file is you dont know what you make                 */
/************************************************************************/

use App\Library\Sform\Sform;
use App\Library\Language\Language;

global $m;
$m = new Sform();

$m->addFormTitle('coolsus');

$m->addFormMethod('post');

$m->addFormCheck('false');

$m->addMess('[french]* désigne un champ obligatoire[/french][english]* required field[/english]');

$m->addSubmitValue('submitS');

$m->addUrl('modules.php');

include 'modules/comments/support/sform/' . $formulaire;

if (!isset($GLOBALS['submitS'])) {
    echo Language::affLangue($m->printForm(''));
} else {
    $message = Language::affLangue($m->affResponse('', 'not_echo', ''));
}
