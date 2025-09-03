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

use App\Library\Url\Url;
use App\Library\Sform\Sform;
use App\Library\Language\Language;

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

$m->addField($ModPathX . '_id', $ModPathX . '_id', '', 'text', true, 11, '', 'a-9');

$m->addKey($ModPathX . '_id');

$m->addSubmitValue('link_fiche_detail');

$m->addUrl('modules.php?ModStart=' . $ModStart . '&ModPath=' . $ModPath);

include_once 'modules' . $ModPathX . '/support/sform/formulaire.php';

// Fabrique le formulaire et assure sa gestion
switch ($link_fiche_detail) {

    case 'fiche_detail':
        if ($m->sformReadMysql($browse_key)) {
            $m->addExtra("<tr><td colspan=\"2\" align=\"center\">");
            $m->addExtra('<a href="javascript: history.go(-1)" class="btn btn-primary">' . translate("Retour en arrière") . '</a>');
            $m->addExtra("</td></tr>");
            $m->keyLock("close");

            echo Language::affLangue($m->printForm("class=\"ligna\""));
        } else {
            Url::redirectUrl($m->url);
        }
        break;

    default:
        if ($m->sformReadMysql($browse_key)) {
            echo '<a class="me-3" href="modules.php?ModStart=' . $ModStart . '&amp;ModPath=' . $ModPath . '&amp;op=fiche_detail&amp;lid=' . $browse_key . '" ><i class="fa fa-info fa-lg" title="' . translate("Détails supplémentaires") . '" data-bs-toggle="tooltip"></i></a>';
        }
        break;
}
