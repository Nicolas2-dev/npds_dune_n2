<?php

use App\Support\Facades\Block;
use App\Support\Facades\Theme;
use App\Support\Facades\Language;
use App\Support\Facades\Metalang;

$moreclass = 'col-12';

switch ($pdst) {

    case '-1':
    case '3':
    case '5':
        echo '</div>
                </div>
            </div>';
        break;

    case '1':
    case '2':
        echo '</div>';

        Theme::colsyst('#col_RB');

        echo '<div id="col_RB" class="collapse show col-lg-3 ">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::rightBlocks($moreclass);

        echo '</div>
                </div>
            </div>
        </div>';
        break;

    case '4':
        echo '</div>';

        Theme::colsyst('#col_LB');

        echo '<div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftBlocks($moreclass);

        echo '</div>
        </div>';

        Theme::colsyst('#col_RB');

        echo '<div id="col_RB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::rightBlocks($moreclass);

        echo '</div>
                </div>
            </div>
        </div>';
        break;

    case '6':
        echo '</div>';

        Theme::colsyst('#col_LB');

        echo '<div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftBlocks($moreclass);

        echo '</div>
                </div>
            </div>
        </div>';
        break;

    default:
        echo '</div>
            </div>
        </div>';
        break;
}

$paths = [
    "themes/{$theme}/Views/partials/footer/footer.php",
    "themes/Base/Views/partials/footer/footer.php",
];

$rep = null;
foreach ($paths as $path) {
    if (is_readable($path)) {
        $rep = $path;
        break;
    }
}

if (! $rep) {
    die('footer.php manquant / not found !<br />');
}

ob_start();
include $rep;
$Xcontent = ob_get_clean();

// ContainerGlobal permet de transmettre · Theme-Dynamic un élément de personnalisation après
// le chargement de footer.html / Si vide alors rien de plus n'est affiché par TD
$ContainerGlobal = '</div>';

if (! empty($ContainerGlobal)) {
    $Xcontent .= $ContainerGlobal;
}

//echo Metalang::metaLang(Language::affLangue($Xcontent));
echo Language::affLangue($Xcontent);


// Ne supprimez pas cette ligne / Don't remove this line
// require_once 'themes/themes-dynamic/footer.php';
//require_once 'themes/base/views/footer.php';
// Ne supprimez pas cette ligne / Don't remove this line

// Editeur::end()

// include externe file from modules/include for functions, codes ...
//footer_before();

//if (file_exists($path_module = 'themes/Base/Bootstrap/footer_before.php')) {
//    include $path_module;
//}

//foot();

//include 'themes/' . $theme . '/Views/partials/footer/footer.php';

//$rep = false;

//settype($ContainerGlobal, 'string');
/*
if (file_exists('themes/' . $theme . '/views/partials/footer/footer.php')) {
    $rep = $theme;
} elseif (file_exists('themes/base/partials/footer/footer.php')) {
    $rep = 'base';
} else {
    echo 'footer.php manquant / not find !<br />';
    die();
}

if ($rep) {
    ob_start();
    include 'themes/' . $rep . '/views/partials/footer/footer.php';
    $Xcontent = ob_get_contents();
    ob_end_clean();

    if ($ContainerGlobal) {
        $Xcontent .= $ContainerGlobal;
    }

    echo Metalang::metaLang(Language::affLangue($Xcontent));
}
*/

$paths = [
    "themes/{$theme}/Views/partials/footer/footer.php",
    "themes/Base/Views/partials/footer/footer.php",
];

$rep = null;
foreach ($paths as $path) {
    if (is_readable($path)) {
        $rep = $path;
        break;
    }
}

if (! $rep) {
    die('footer.php manquant / not found !<br />');
}

ob_start();
include $rep;
$Xcontent = ob_get_clean();

// ContainerGlobal permet de transmettre · Theme-Dynamic un élément de personnalisation après
// le chargement de footer.html / Si vide alors rien de plus n'est affiché par TD
$ContainerGlobal = '</div>';

if (! empty($ContainerGlobal)) {
    $Xcontent .= $ContainerGlobal;
}

//echo Metalang::metaLang(Language::affLangue($Xcontent));
echo Language::affLangue($Xcontent);

// footer

//if ($user) {
//    $cookie9 = $ibix[0];
//}


// include externe file from modules/themes include for functions, codes ...
//if (isset($user)) {
//    global $cookie9;
//    footer_after($cookie9);
    //if (file_exists($path_theme = 'themes/' . $cookie9 . '/Bootstrap/footer_after.php')) {
    //    include $path_theme;
    //} else {
    //    if (file_exists($path_module = 'themes/Base/Bootstrap/footer_after.php')) {
    //        include $path_module;
    //    }
    //}
//} else {
//    global $Default_Theme;
//    footer_after($Default_Theme);

    //if (file_exists($path_theme = 'themes/' . $Default_Theme . '/Bootstrap/footer_after.php')) {
    //    include $path_theme;
    //} else {
    //    if (file_exists($path_module = 'themes/Base/Bootstrap/footer_after.php')) {
    //        include $path_module;
    //    }
    //}

//}

//$theme = isset($user) ? $cookie9 : $Default_Theme;
//
//if (is_readable($theme_file  = "themes/".$theme."/include/footer_after.inc")) {
//    include $theme_file;
//} elseif (is_readable($module_file = 'modules/include/footer_after.inc')) {
//    include $module_file;
//}


//echo '</body>
//</html>';

// faire listener ou middleware
//include 'sitemap.php';

//sql_close();
