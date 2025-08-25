<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

// DEFINITION et CAST VARIABLES
settype($title, 'string');
settype($post, 'string');
settype($nuke_url, 'string');
settype($ModPath, 'string');
settype($title, 'string');

global $nuke_url, $language;
// ----------------------------

$PAGES['index.php']['title'] = "[french]Index[/french][english]Home[/english][spanish]Index[/spanish][german]Index[/german][chinese]&#x7D22;&#x5F15;[/chinese]+";
$PAGES['index.php']['blocs'] = "0";
$PAGES['index.php']['run'] = "yes";
$PAGES['index.php']['sitemap'] = "0.8";

$PAGES['user.php']['title'] = "[french]Section membre pour personnaliser le site[/french][english]Your personal page to customize the site [/english][spanish]Secci&oacute;n para personalizar el sitio[/spanish][german]Mitglied Abschnitt auf der Website anpassen[/german][chinese]&#x4E2A;&#x4EBA;&#x8BBE;&#x7F6E;&#x9875;&#x9762;, &#x5141;&#x8BB8;&#x4F7F;&#x7528;&#x6237;&#x7684;&#x7AD9;&#x70B9;&#x5B9E;&#x73B0;&#x4E2A;&#x4EBA;&#x5316;[/chinese]+";
$PAGES['user.php']['blocs'] = "0";
$PAGES['user.php']['run'] = "yes";

$PAGES['user.php?op=editjournal']['title'] = "[french]Edition du journal utilisateur[/french][english][/english][spanish][/spanish][german][/german][chinese][/chinese]+";
$PAGES['user.php?op=editjournal']['blocs'] = "0";
$PAGES['user.php?op=editjournal']['run'] = "yes";
$PAGES['user.php?op=editjournal']['TinyMce'] = 1;
$PAGES['user.php?op=editjournal']['TinyMce-theme'] = "short";

$PAGES['memberslist.php']['title'] = "[french]Liste des membres[/french][english]Members list[/english][spanish]Lista de Miembros[/spanish][german]Mitglieder[/german][chinese]&#x4F1A;&#x5458;&#x5217;&#x8868;[/chinese]+";
$PAGES['memberslist.php']['blocs'] = "0";
$PAGES['memberslist.php']['run'] = "yes";

$PAGES['searchbb.php']['title'] = "[french]Recherche dans les forums[/french][english]Search in the forums[/english][spanish]B&uacute;squeda en los foros[/spanish][german]Die Foren durchsuchen[/german][chinese]&#x5728;&#x8BBA;&#x575B;&#x4E2D;&#x67E5;&#x627E;[/chinese]+";
$PAGES['searchbb.php']['blocs'] = "0";
$PAGES['searchbb.php']['run'] = "yes";

$PAGES['article.php']['title'] = "$title+";
$PAGES['article.php']['blocs'] = "0";
$PAGES['article.php']['run'] = "yes";
$PAGES['article.php']['sitemap'] = "1";

$PAGES['submit.php']['title'] = "[french]Soumettre un nouvel article[/french][english]Submit a new[/english][spanish]Someter una noticia[/spanish][german]Einen neuen Artikel[/german][chinese]&#x63D0;&#x4EA4;&#x4E00;&#x7BC7;&#x65B0;&#x6587;&#x7AE0;[/chinese]+";
$PAGES['submit.php']['blocs'] = "0";
$PAGES['submit.php']['run'] = "yes";
$PAGES['submit.php']['TinyMce'] = 1;
$PAGES['submit.php']['TinyMce-theme'] = "full";

$PAGES['sections.php']['title'] = "[french]Les articles de fond[/french][english]Articles in special sections[/english][spanish]Art&iacute;culos especiales[/spanish][german]Fachartikel[/german][chinese]&#x4E3B;&#x9898;&#x6027;&#x6587;&#x7AE0;[/chinese]+|$title+";
$PAGES['sections.php']['blocs'] = "1";
$PAGES['sections.php']['run'] = "yes";
$PAGES['sections.php']['sitemap'] = "0.8";

$PAGES['faq.php']['title'] = "[french]FAQs / Questions Fr&eacute;quentes[/french][english]FAQs (Frequently Asked Question)[/english][spanish]Preguntas frecuentes[/spanish][german]FAQs[/german][chinese]&#x5E38;&#x89C1;&#x95EE;&#x9898; (FAQ)[/chinese]+|$title+";
$PAGES['faq.php']['blocs'] = "0";
$PAGES['faq.php']['run'] = "yes";

$PAGES['download.php']['title'] = "[french]Les t&eacute;l&eacute;chargements[/french][english]Downloads[/english][spanish]Descargas[/spanish][german]Downloads[/german][chinese]&#x4E0B;&#x8F7D;[/chinese]+|$title+";
$PAGES['download.php']['run'] = "yes";
$PAGES['download.php']['sitemap'] = "0.8";

$PAGES['topics.php']['title'] = "[french]Les sujets actifs[/french][english]The actives topics[/english][spanish]Temas activos[/spanish][german]Aktive Themen[/german][chinese]&#x5F53;&#x524D;&#x6D3B;&#x8DC3;&#x7684;&#x4E3B;&#x9898;[/chinese]+";
$PAGES['topics.php']['blocs'] = "1";
$PAGES['topics.php']['run'] = "yes";

$PAGES['search.php']['title'] = "[french]Rechercher dans les sujets[/french][english]Search in the topics[/english][spanish]Buscar en este Temas[/spanish][german]Suche in diesem Themen[/german][chinese]&#x5728;&#x4E3B;&#x9898;&#x4E2D;&#x67E5;&#x627E;[/chinese]+";
$PAGES['search.php']['blocs'] = "1";
$PAGES['search.php']['run'] = "yes";

$PAGES['friend.php']['title'] = "[french]Envoyer un Article / Pr&eacute;venir un ami[/french][english]Send Story to a Friend[/english][spanish]Enviar el art&iacute;culo[/spanish][german]Artikel versenden[/german][chinese]&#x53D1;&#x9001;&#x4E00;&#x7BC7;&#x6587;&#x7AE0; / &#x901A;&#x77E5;&#x53CB;&#x4EBA;[/chinese]+|$title+";
$PAGES['friend.php']['blocs'] = "1";
$PAGES['friend.php']['run'] = "yes";

$PAGES['top.php']['title'] = "[french]Le top du site[/french][english]Top[/english][spanish]Top[/spanish][german]Top[/german][chinese]&#x4F18;&#x79C0;&#x7AD9;&#x70B9;[/chinese]+";
$PAGES['top.php']['blocs'] = "0";
$PAGES['top.php']['run'] = "yes";
$PAGES['top.php']['sitemap'] = "0.5";

$PAGES['stats.php']['title'] = "[french]Statistiques du site[/french][english]Web site statistics[/english][spanish]Estad&iacute;sticas del sitio[/spanish][german]Site-Statistik[/german][chinese]&#x884C;&#x7EDF;&#x8BA1;[/chinese]+";
$PAGES['stats.php']['blocs'] = "1";
$PAGES['stats.php']['run'] = "yes";
$PAGES['stats.php']['sitemap'] = "0.5";

$PAGES['admin.php']['title'] = ""; // obligatoirement à vide
$PAGES['admin.php']['blocs'] = "0";
$PAGES['admin.php']['run'] = "yes";
$PAGES['admin.php']['css'] = array($nuke_url . "/themes/base/assets/css/admin.css+");

/*
$PAGES['admin.php']['TinyMce']=1;
$PAGES['admin.php']['TinyMce-theme']="full";
$PAGES['admin.php']['TinyMceRelurl']="false";
*/

// ==> éviter un chargement de tiny à toutes les pages admin //
// page nécessitant tiny appelée par l'url admin.php + variable op transmise par POST
if (isset($_POST['op'])) {
   if ($_POST['op'] == 'PreviewAdminStory') {
      $PAGES['admin.php']['TinyMce'] = 1;
      $PAGES['admin.php']['TinyMce-theme'] = "full";
      $PAGES['admin.php']['TinyMceRelurl'] = "false";
   }
}

// page nécessitant tiny appelée par l'url admin.php + variable op transmise dans l'url
$adm_op_url = array(
   'adminStory', 
   'DisplayStory', 
   'PreviewAgain', 
   'EditStory', 
   'autoEdit', 
   'Edito_load', 
   'sections', 
   'sectionedit', 
   'new_rub_section', 
   'rubriquedit', 
   'secartedit', 
   'secartupdate', 
   'DownloadAdmin', 
   'DownloadEdit', 
   'email_user', 
   'FaqCatGo', 
   'lnl_Shw_Body', 
   'lnl_Shw_Footer', 
   'lnl_Shw_Header', 
   'links', 
   'LinksModLink', 
   'Add_Footer'
);

foreach ($adm_op_url as $v) {
   $PAGES['admin.php?op=' . $v]['title'] = ""; // obligatoirement à vide
   $PAGES['admin.php?op=' . $v]['blocs'] = "0";
   $PAGES['admin.php?op=' . $v]['run'] = "yes";
   $PAGES['admin.php?op=' . $v]['TinyMce'] = 1;
   $PAGES['admin.php?op=' . $v]['TinyMce-theme'] = "full";
   $PAGES['admin.php?op=' . $v]['css'] = array($nuke_url . "/themes/base/assets/css/admin.css+");
   $PAGES['admin.php?op=' . $v]['TinyMceRelurl'] = "false";
}

$PAGES['forum.php']['title'] = "[french]Les forums de discussion[/french][english]Forums[/english][spanish]Foros de discusi&oacute;n[/spanish][german]Diskussionsforen[/german][chinese]&#x7248;&#x9762;&#x7BA1;&#x7406;[/chinese]+";
$PAGES['forum.php']['run'] = "yes";
$PAGES['forum.php']['sitemap'] = "0.9";
$PAGES['forum.php']['meta-keywords'] = "forum,forums,discussion,discussions,aide,entraide,échange,échanges";
$PAGES['forum.php']['blocs'] = "0";

$PAGES['viewforum.php']['title'] = "[french]Forum[/french][english]Forum[/english][spanish]Foro[/spanish][german]Forum[/german][chinese]&#x7248;&#x9762;&#x7BA1;&#x7406;[/chinese] : $title+";
$PAGES['viewforum.php']['run'] = "yes";

$PAGES['viewtopic.php']['title'] = "[french]Forum[/french][english]Forum[/english][spanish]Foro[/spanish][german]Forum[/german][chinese]&#x7248;&#x9762;&#x7BA1;&#x7406;[/chinese] : $title / $post+";
$PAGES['viewtopic.php']['run'] = "yes";
$PAGES['viewtopic.php']['css'] = array($nuke_url . '/assets/shared/bootstrap/dist/css/bootstrap-icons.css+');

$PAGES['viewtopicH.php']['title'] = "[french]Forum[/french][english]Forum[/english][spanish]Foro[/spanish][german]Forum[/german][chinese]&#x7248;&#x9762;&#x7BA1;&#x7406;[/chinese] : $title / $post+";
$PAGES['viewtopicH.php']['run'] = "yes";
$PAGES['viewtopicH.php']['css'] = array($nuke_url . '/assets/shared/bootstrap/dist/css/bootstrap-icons.css+');

$PAGES['reply.php']['title'] = "[french]R&eacute;pondre &#xE0; un post sur le forum[/french][english]Forum : reply to a post[/english][spanish]Responder a un mensaje en el foro[/spanish][german]Antwort auf einen Beitrag im Forum[/german][chinese]&#x56DE;&#x590D;&#x8BBA;&#x575B;&#x4E2D;&#x7684;&#x4E00;&#x4E2A;&#x5E16;&#x5B50;[/chinese]+";
$PAGES['reply.php']['run'] = "yes";

$PAGES['replyH.php']['title'] = "[french]R&eacute;pondre &#xE0; un post sur le forum[/french][english]Forum : reply to a post[/english][spanish]Responder a un mensaje en el foro[/spanish][german]Antwort auf einen Beitrag im Forum[/german][chinese]&#x56DE;&#x590D;&#x8BBA;&#x575B;&#x4E2D;&#x7684;&#x4E00;&#x4E2A;&#x5E16;&#x5B50;[/chinese]+";
$PAGES['replyH.php']['run'] = "yes";

$PAGES['newtopic.php']['title'] = "[french]Poster un nouveau sujet[/french][english]Post a new topic[/english][spanish]Publicar nuevo tema[/spanish][german]Neues Thema erˆffnen[/german][chinese]&#x5F20;&#x8D34;&#x4E00;&#x4E2A;&#x65B0;&#x4E3B;&#x9898;[/chinese]+";
$PAGES['newtopic.php']['run'] = "yes";

$PAGES['topicadmin.php']['title'] = "[french]Gestion des forums[/french][english]Forum admin[/english][spanish]Gesti&oacute;n de los foros[/spanish][german]Management-Foren[/german][chinese]&#x5BF9;&#x8BBA;&#x575B;&#x7684;&#x7BA1;&#x7406;[/chinese]+";
$PAGES['topicadmin.php']['run'] = "yes";

$PAGES['editpost.php']['title'] = "";
$PAGES['editpost.php']['run'] = "yes";

$PAGES['reviews.php']['title'] = "[french]Les critiques[/french][english]Reviews[/english][spanish]los cr&iacute;ticos[/spanish][german]Kritik[/german][chinese]&#x8BC4;&#x8BBA;[/chinese]+";
$PAGES['reviews.php']['blocs'] = "1";
$PAGES['reviews.php']['run'] = "yes";

$PAGES['abla.php']['title'] = "[french]Admin Blackboard[/french][english]Admin Blackboard[/english][spanish]Admin Blackboard[/spanish][german]Admin Blackboard[/german][chinese]Admin Blackboard[/chinese]+";
$PAGES['abla.php']['run'] = "yes";
$PAGES['abla.php']['blocs'] = "0";

$PAGES['replypmsg.php']['title'] = "[french]R&eacute;pondre &agrave; un MP[/french][english]Reply to a MP[/english][spanish]Responder a un MP[/spanish][german]Antwort auf eine MP[/german][chinese]Reply to a MP[/chinese]+";
$PAGES['replypmsg.php']['run'] = "yes";
$PAGES['replypmsg.php']['blocs'] = "1";

$PAGES['readpmsg.php']['title'] = "[french]Lire un MP[/french][english]Read a MP[/english][spanish]Leer un MP[/spanish][german]Lesen Sie einen MP[/german][chinese]Read a MP[/chinese]+";
$PAGES['readpmsg.php']['run'] = "yes";
$PAGES['readpmsg.php']['blocs'] = "1";

$PAGES['map.php']['title'] = "[french]Plan du Site[/french][english]SiteMap[/english][spanish]Mapa del Sitio[/spanish][german]Site Map[/german][chinese]SiteMap[/chinese]";
$PAGES['map.php']['blocs'] = "0";
$PAGES['map.php']['run'] = "yes";

$PAGES['pollBooth.php']['title'] = "[french]Les Sondages[/french][english]Opinion poll[/english][spanish]las encuestas[/spanish][german]die Umfragen[/german][chinese]Opinion poll[/chinese]";
$PAGES['pollBooth.php']['blocs'] = "2";
$PAGES['pollBooth.php']['run'] = "yes";

// Page static
$PAGES['static.php?op=statik.txt']['title'] = "[french]Page de d&eacute;monstration[/french][english]Demo page[/english][spanish]Demostraci&oacute;n p&aacute;gina[/spanish][german]Demo-Seite[/german][chinese]Demo page[/chinese]+";
$PAGES['static.php?op=statik.txt']['blocs'] = "-1";
$PAGES['static.php?op=statik.txt']['run'] = "yes";

// Modules
// Pour les modules il existe deux forme d'écriture :
// la syntaxe : modules.php?ModPath=links&ModStart=links ==> qui permet d'affecter un titre, un run et un type de bloc pour chaque 'sous-url' du module
// la syntaxe : mdoules.php?ModPath=links&ModStart=links* (rajout d'une * à la fin) ==> qui permet de faire la même chose mais en indiquant que TOUTES les pages du module seront traitées de la même manière
$PAGES['modules.php?ModPath=links&ModStart=links*']['title'] = "[french]Liens et annuaires[/french][english]Web Links[/english][spanish]Enlaces y Directorios[/spanish][german]Links und Verzeichnisse[/german][chinese]&#x7F51;&#x7AD9;&#x94FE;&#x63A5;[/chinese]+|$title+";
$PAGES['modules.php?ModPath=links&ModStart=links*']['run'] = "yes";
$PAGES['modules.php?ModPath=links&ModStart=links*']['blocs'] = "2";
$PAGES['modules.php?ModPath=links&ModStart=links*']['TinyMce'] = 1;
$PAGES['modules.php?ModPath=links&ModStart=links*']['TinyMce-theme'] = "short";

$PAGES['modules.php?ModPath=links/admin&ModStart=links*']['title'] = "[french]Administration des liens et annuaires[/french][english]Web Links[/english][spanish]Gesti&oacute;n de enlaces y directorios[/spanish][german]Verwaltung Links und Verzeichnisse[/german][chinese]&#x7F51;&#x7AD9;&#x94FE;&#x63A5;[/chinese]+|$title+";
$PAGES['modules.php?ModPath=links/admin&ModStart=links*']['run'] = "yes";
$PAGES['modules.php?ModPath=links/admin&ModStart=links*']['blocs'] = "2";
$PAGES['modules.php?ModPath=links/admin&ModStart=links*']['TinyMce'] = 1;
$PAGES['modules.php?ModPath=links/admin&ModStart=links*']['TinyMce-theme'] = "full";

$PAGES['modules.php?ModPath=comments&ModStart=reply*']['title'] = "[french]Commentaires[/french][english]Comments[/english][spanish]Comentarios[/spanish][german]Kommentare[/german][chinese]Comments[/chinese]";
$PAGES['modules.php?ModPath=comments&ModStart=reply*']['run'] = "yes";
$PAGES['modules.php?ModPath=comments&ModStart=reply*']['blocs'] = "2";
$PAGES['modules.php?ModPath=comments&ModStart=reply*']['TinyMce'] = 0;
$PAGES['modules.php?ModPath=comments&ModStart=reply*']['TinyMce-theme'] = "short";

$PAGES['modules.php?ModPath=contact&ModStart=contact']['title'] = "[french]Nous Contacter[/french][english]Contact us[/english][spanish]Contacte con nosotros[/spanish][german]Kontakt[/german][chinese]Contact us[/chinese]-";
$PAGES['modules.php?ModPath=contact&ModStart=contact']['run'] = "yes";
$PAGES['modules.php?ModPath=contact&ModStart=contact']['blocs'] = "0";

$PAGES['modules.php?ModPath=archive-stories&ModStart=archive-stories*']['title'] = "[french]Les Nouvelles[/french][english]News[/english][spanish]Noticias[/spanish][german]Nachrichten[/german][chinese]News[/chinese]+";
$PAGES['modules.php?ModPath=archive-stories&ModStart=archive-stories*']['run'] = "yes";
$PAGES['modules.php?ModPath=archive-stories&ModStart=archive-stories*']['blocs'] = "0";


// CSS sur fichiers particuliers car n'utilisant pas header.php
$PAGES['chatrafraich.php']['css'] = array("chat.css+");
$PAGES['chatinput.php']['css'] = array("chat.css+");

$PAGES['modules.php?ModPath=reseaux-sociaux&ModStart=reseaux-sociaux*']['title'] = "[french]R&eacute;seaux Sociaux[/french][english]Social Networks[/english]";
$PAGES['modules.php?ModPath=reseaux-sociaux&ModStart=reseaux-sociaux*']['run'] = "yes";
$PAGES['modules.php?ModPath=reseaux-sociaux&ModStart=reseaux-sociaux*']['blocs'] = "0";

$PAGES['modules.php?ModPath=wspad&ModStart=wspad*']['title'] = "[french]WS-Pad[/french][english]WS-PAd[/english][spanish]WS-Pad[/spanish][german]WS-Pad[/german][chinese]WS-Pad[/chinese]+|$title+";
$PAGES['modules.php?ModPath=wspad&ModStart=wspad*']['run'] = "yes";
$PAGES['modules.php?ModPath=wspad&ModStart=wspad*']['blocs'] = "0";
$PAGES['modules.php?ModPath=wspad&ModStart=wspad*']['TinyMce'] = 1;
$PAGES['modules.php?ModPath=wspad&ModStart=wspad*']['TinyMce-theme'] = "full+setup";
$PAGES['modules.php?ModPath=wspad&ModStart=wspad*']['css'] = array($nuke_url . "/assets/shared/bootstrap/dist/css/bootstrap-icons.css+");

// Filtre sur l'URI
// $PAGES['forum=1']['title']="forum.php";
// $PAGES['forum=1']['run']="user";

$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['title'] = "[french]Galerie d'images[/french][english]Pictures galery[/english][spanish]Galeria de imagenes[/spanish]+";
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['js'] = array($nuke_url . '/modules/npds_galerie/js/jquery.watermark.min.js');
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['run'] = "yes";
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['blocs'] = "0";
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['TinyMce'] = 1;
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['TinyMce-theme'] = "short";
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['css'] = array($nuke_url . '/modules/npds_galerie/css/galerie.css+', $nuke_url . "/assets/shared/bootstrap/dist/css/bootstrap-icons.css+");

$PAGES['modules.php?ModPath=geoloc&ModStart=geoloc*']['title'] = "[french]Localisation[/french][english]Geolocation[/english][spanish]Geolocalizaci&oacute;n[/spanish][german]Geolocation[/german][chinese]&#22320;&#29702;&#20301;&#32622;[/chinese]+|$title+";
$PAGES['modules.php?ModPath=geoloc&ModStart=geoloc*']['run'] = "yes";
$PAGES['modules.php?ModPath=geoloc&ModStart=geoloc*']['blocs'] = "-1";
$PAGES['modules.php?ModPath=geoloc&ModStart=geoloc*']['css'] = array($nuke_url . '/assets/shared/ol/ol.css+', $nuke_url . '/modules/geoloc/include/css/geoloc_style.css+');
