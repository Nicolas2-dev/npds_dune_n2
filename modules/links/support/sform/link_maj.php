<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2020 by Philippe Brunier   */
/*                                                                      */
/* New Links.php Module with SFROM extentions                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

use App\Library\Sform\Sform;

global $ModPath, $ModStart;

$pos = strpos($ModPath, '/admin');

if ($pos > 0) {
   $ModPathX = substr($ModPath, 0, $pos);
} else {
   $ModPathX = $ModPath;
}

global $m;
$m = new Sform();

$m->addFormTitle($ModPathX);

$m->addFormMethod('get');

$m->addFormCheck('true');

$m->addField($ModPathX . '_id', $ModPathX . '_id', $browse_key, 'text', true, 11, '', 'a-9');

$m->addKey($ModPathX . '_id');

$m->addUrl('modules.php');

$m->addSubmitValue('modifylinkrequest_adv_infos');

$m->addField('ModStart', '', $ModStart, 'hidden', false);

$m->addField('ModPath', '', $ModPath, 'hidden', false);

if (isset($author)) {
   $m->addField('author', '', $author, 'hidden', false);
}

$m->addField('op', '', 'modifylinkrequest', 'hidden', false);

$m->addField('lid', '', $browse_key, 'hidden', false);

include_once 'modules/' . $ModPathX . '/support/sform/formulaire.php';

// Fabrique le formulaire et assure sa gestion
function interface_function($browse_key)
{
   global $m;

   if ($m->sformReadMysql($browse_key)) {
      $m->addField('', '', translate('Mise à jour'), 'submit', false);
      $m->addExtra(' - ');
      $m->addField('', '', translate('Effacer'), 'submit', false);
   } else {
      $m->addField('', '', translate('Ajouter'), 'submit', false);
   }

   $m->keyLock('close');

   echo $m->printForm('class="ligna"');
}

function Supprimer_function($browse_key)
{
   global $m;

   $m->sformReadMysql($browse_key);
   $m->form_key_value = $browse_key;
   $m->sformDeleteMysql();
}

switch ($modifylinkrequest_adv_infos) {

   case translate('Ajouter'):
      $m->makeResponse();
      $m->sformInsertMysql($m->answer);

      interface_function($browse_key);
      break;

   case translate('Effacer'):
      $m->makeResponse();
      $m->sformDeleteMysql();

      interface_function($browse_key);
      break;

   case 'Supprimer_MySql':
      // C'est normal que ce case soit vide !
      break;

   case translate('Mise à jour'):
      $m->makeResponse();
      $m->sformModifyMysql($m->answer);

      interface_function($browse_key);
      break;

   default:
      interface_function($browse_key);
      break;
}
