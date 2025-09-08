<?php

use App\Library\Block\Block;

/************************************************************************/
/* Theme for NPDS / Net Portal Dynamic System                           */
/*======================================================================*/
/* This theme use the NPDS theme-dynamic engine (Meta-Lang)             */
/*                                                                      */
/* Theme : npds-boost_sk 2015 by jpb                                    */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

//////////// a integrer dans le layout

// -----------------------
$header = 1;
// -----------------------

// include externe file from themes/base/bootstrap/ for functions, codes ...
if (file_exists('themes/base/bootstrap/header_before.php')) {
    include 'themes/base/bootstrap/header_before.php';
}

// mis dans lib theme provisoirement a revoir !
head($tiny_mce_init, $css_pages_ref, $css, $tmp_theme, $skin, $js, $m_description, $m_keywords);

// faire listener
refererUpdate();

// faire listener
counterUpdate();


// include externe file from themes/base/bootstrap/ for functions, codes ...
if (file_exists('themes/base/bootstrap/header_after.php')) {
    include 'themes/base/bootstrap/header_after.php';
}

///////////////

/*
 * Nomination des div par l'attribut id:
 * col_princ contient le contenu principal
 * col_LB contient les blocs historiquement dit de gauche
 * col_RB contient les blocs historiquement dit de droite
 * 
 * Dans ce thème la variable $pdst permet de gérer le nombre et la disposition (de gauche à droite) des colonnes.
 * '-1' -> col_princ
 * '0'  -> col_LB + col_princ
 * '1'  -> col_LB + col_princ + col_RB
 * '2'  -> col_princ + col_RB
 * '3'  -> col_LB + col_RB + col_princ
 * '4'  -> col_princ + col_LB + col_RB
 * '5'  -> col_RB + col_princ
 * '6'  -> col_princ + col_LB
 *     
 * La gestion de ce paramètre s'effectue dans le fichier 'pages.php' du dossier 'themes
 */

$coltarget = '';

global $pdst;

$blg_actif = sql_query("SELECT * 
                        FROM " . sql_prefix('lblocks') . " 
                        WHERE actif ='1'");

$nb_blg_actif = sql_num_rows($blg_actif);

sql_free_result($blg_actif);

if ($nb_blg_actif == 0) {

    switch ($pdst) {

        case '0':
            $pdst = '-1';
            break;

        case '1':
            $pdst = '2';
            break;

        case '3':
            $pdst = '5';
            break;

        case '4':
            $pdst = '2';
            break;

        case '6':
            $pdst = '-1';
            break;
    }
}

$bld_actif = sql_query("SELECT * 
                        FROM " . sql_prefix('rblocks') . " 
                        WHERE actif ='1'");

$nb_bld_actif = sql_num_rows($bld_actif);

sql_free_result($bld_actif);

if ($nb_bld_actif == 0) {
    switch ($pdst) {

        case '1':
            $pdst = '0';
            break;

        case '2':
            $pdst = '-1';
            break;

        case '3':
            $pdst = '0';
            break;

        case '4':
            $pdst = '6';
            break;

        case '5':
            $pdst = '-1';
            break;
    }
}

function colsyst($coltarget)
{
    $coltoggle = '<div class="col d-lg-none me-2 my-2">
        <hr />
        <a class=" small float-end" href="#" data-bs-toggle="collapse" data-bs-target="' . $coltarget . '">
            <span class="plusdecontenu trn">Plus de contenu</span>
        </a>
    </div>';

    echo $coltoggle;
}

// ContainerGlobal permet de transmettre à Theme-Dynamic un élément de personnalisation avant
// le chargement de header.html / Si vide alors la class body est chargée par défaut par TD
$ContainerGlobal = '<div id="container">';

// Ne supprimez pas cette ligne / Don't remove this line
//require_once 'themes/base/views/header.php';

global $theme, $Start_Page;

$rep = false;

$Start_Page = str_replace('/', '', $Start_Page);

settype($ContainerGlobal, 'string');

if (file_exists('themes/' . $theme . '/views/partials/header/header.php')) {
    $rep = $theme;
} elseif (file_exists('themes/base/views/partials/header/header.php')) {
    $rep = 'base';
} else {
    echo 'header.php manquant / not find !<br />';
    die();
}

if ($rep) {
    if (file_exists('themes/base/bootstrap/body_onload.php') || file_exists('themes/' . $theme . '/bootstrap/body_onload.php')) {
        $onload_init = ' onload="init();"';
    } else {
        $onload_init = '';
    }

    if (!$ContainerGlobal) {
        echo '<body' . $onload_init . ' class="body" data-bs-theme="' . $theme_darkness . '">';
    } else {
        echo '<body' . $onload_init . ' data-bs-theme="' . $theme_darkness . '">';
        echo $ContainerGlobal;
    }

    ob_start();
    // landing page
    if (stristr($_SERVER['REQUEST_URI'], $Start_Page) && file_exists('themes/' . $rep . '/views/partials/header/header_landing.php')) {
        include 'themes/' . $rep . '/views/partials/header/header_landing.php';
    } else {
        include 'themes/' . $rep . '/views/partials/header/header.php';
    }

    $Xcontent = ob_get_contents();
    ob_end_clean();

    echo Metalang::metaLang(Language::affLangue($Xcontent));
}

// powerpack deprecated !
//global $powerpack;
//if (!isset($powerpack)) {
//    include 'powerpack.php';
//}
// Ne supprimez pas cette ligne / Don't remove this line

/************************************************************************/
/*     Le corps de page de votre Site - En dessous du Header            */
/*     On Ouvre les Différent Blocs en Fonction de la Variable $pdst    */
/*                         Le corps englobe :                           */
/*                 col_LB + col_princ + col_RB                          */
/*           Si Aucune variable pdst dans pages.php                     */
/*   ==> Alors affichage par defaut : col_LB + col_princ soit $pdst=0   */
/* =====================================================================*/

$moreclass = 'col';

echo '<div id="corps" class="container-fluid n-hyphenate">
    <div class="row g-3">';

switch ($pdst) {

    case '-1':
        echo '<div id="col_princ" class="col-12">';
        break;

    case '1':
        colsyst('#col_LB');

        echo '<div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftBlocks($moreclass);

        echo '</div>
            </div>
        <div id="col_princ" class="col-lg-6">';
        break;

    case '2':
    case '6':
        echo '<div id="col_princ" class="col-lg-9">';
        break;

    case '3':
        colsyst('#col_LB');

        echo '<div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftBlocks($moreclass);

        echo '</div>
        </div>';

        colsyst('#col_RB');

        echo '<div id="col_RB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::rightBlocks($moreclass);

        echo '</div>
            </div>
        <div id="col_princ" class="col-lg-6">';
        break;

    case '4':
        echo '<div id="col_princ" class="col-lg-6">';
        break;

    case '5':
        colsyst('#col_RB');

        echo '<div id="col_RB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::rightBlocks($moreclass);

        echo '</div>
            </div>
            <div id="col_princ" class="col-lg-9">';
        break;

    default:
        colsyst('#col_LB');

        echo '<div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftBlocks($moreclass);

        echo '</div>
            </div>
        <div id="col_princ" class="col-lg-9">';
        break;
}
