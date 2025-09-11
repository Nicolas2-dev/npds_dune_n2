<?php

/************************************************************************/
/* SFORM Extender for NPDS USER                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

/************************************************************************/
/* Dont modify this file if you dont know what you make                 */
/************************************************************************/
/* Utilise une table complémentaire de la table user : users_extend
   C1  varchar(255)
   C2  varchar(255)
   C3  varchar(255)
   C4  varchar(255)
   C5  varchar(255)
   C6  varchar(255)
   C7  varchar(255)
   C8  varchar(255)

   M1  mediumtext
   M2  mediumtext // utilisé pour les réseaux sociaux

   T1  varchar(10) date standard
   T2  varchar(14) peut stocker un TimeStamp

   B1  BLOB peut stocker des fichiers (gif, exe ...)

   ==> Le nom des champs (C1, C2, M1, T1 ...) est IMPERATIF
   ==> un formulaire valide doit contenir au moins C1 ou M1 ou T1
*/

use App\Support\Facades\Language;

if (!isset($C1)) {
    $C1 = '';
}

if (!isset($C2)) {
    $C2 = '';
}

if (!isset($C3)) {
    $C3 = '';
}

if (!isset($C4)) {
    $C4 = '';
}

if (!isset($C5)) {
    $C5 = '';
}

if (!isset($C6)) {
    $C6 = '';
}

if (!isset($C7)) {
    $C7 = '';
}

if (!isset($C8)) {
    $C8 = '';
}

if (!isset($T1)) {
    $T1 = '';
}

if (!isset($T1)) {
    $T2 = '';
}

if (!isset($M2)) {
    $M2 = '';
}

$m->addComment(Language::affLangue('<div class="row"><p class="lead">[french]En savoir plus[/french][english]More[/english][spanish]M&#xE1;s[/spanish][german]Mehr[/german]</p></div>'));

$m->addField('C1', Language::affLangue('[french]Activit&#x00E9; professionnelle[/french][english]Professional activity[/english][spanish]Actividad profesional[/spanish][german]Berufliche T&#xE4;tigkeit[/german]'), $C1, 'text', false, 100, '', '');

$m->addExtender('C1', '', '<span class="help-block text-end" id="countcar_C1"></span>');

$m->addField('C2', Language::affLangue('[french]Code postal[/french][english]Postal code[/english][spanish]C&#xF3;digo postal[/spanish][german]Postleitzahl[/german]'), $C2, 'text', false, 5, '', '');

$m->addExtender('C2', '', '<span class="help-block text-end" id="countcar_C2"></span>');

$m->addDate('T1', Language::affLangue('[french]Date de naissance[/french][english]Birth date[/english][spanish]Fecha de nacimiento[/spanish][german]Geburtsdatum[/german]'), $T1, 'text', '', false, 20);

$m->addExtender('T1', '', '<span class="help-block">JJ/MM/AAAA</span>');

$m->addField('M2', 'R&#x00E9;seaux sociaux', $M2, 'hidden', false);

include 'modules/geoloc/config/config.php';

$m->addComment(Language::affLangue('<div class="row"><p class="lead"><a href="modules.php?ModPath=geoloc&amp;ModStart=geoloc"><i class="fas fa-map-marker-alt fa-2x" title="[french]Modifier ou d&#xE9;finir votre position[/french][english]Define or change your geolocation[/english][chinese]Define or change your geolocation[/chinese][spanish]Definir o cambiar la geolocalizaci&#243;n[/spanish][german]Definieren oder &#xE4;ndern Sie Ihre Geolokalisierung[/german]" data-bs-toggle="tooltip" data-bs-placement="right"></i></a>&nbsp;[french]G&#xE9;olocalisation[/french][english]Geolocation[/english][chinese]&#x5730;&#x7406;&#x5B9A;&#x4F4D;[/chinese][spanish]Geolocalizaci&#243;n[/spanish][german]Geolokalisierung[/german]</p></div>'));

$m->addField($ch_lat, Language::affLangue('[french]Latitude[/french][english]Latitude[/english][chinese]&#x7ECF;&#x5EA6;[/chinese][spanish]Latitud[/spanish][german]Breitengrad[/german]'), $$ch_lat, 'text', false, '', '', 'lat');

$m->addField($ch_lon, Language::affLangue('[french]Longitude[/french][english]Longitude[/english][chinese]&#x7EAC;&#x5EA6;[/chinese][spanish]Longitud[/spanish][german]L&#228;ngengrad[/german]'), $$ch_lon, 'text', false, '', '', 'long');

// Les champ B1 et M2 sont utilisé par NPDS dans le cadre des fonctions USERs
// Si vous avez besoin d'un ou de champs ci-dessous - le(s) définir selon vos besoins et l'(les) enlever du tableau $fielddispo
$fielddispo = array('C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'M1', 'T2');

$geofield = array($ch_lat, $ch_lon);

$fieldrest = array_diff($fielddispo, $geofield);

//reset($fieldrest);
foreach ($fieldrest as $k => $v) {
    $m->addField($v, $v, '', 'hidden', false);
}

$m->addExtra('
<script type="text/javascript" src="assets/shared/flatpickr/dist/flatpickr.min.js"></script>
<script type="text/javascript" src="assets/shared/flatpickr/dist/l10n/' . Language::languageIso(1, '', '') . '.js"></script>
<script type="text/javascript">
    //<![CDATA[
        $(document).ready(function() {
            $("<link>").appendTo("head").attr({type: "text/css", rel: "stylesheet",href: "assets/shared/flatpickr/dist/themes/npds.css"});
        })
    //]]>
</script>');
