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

/************************************************************************/
/* Fermeture ou ouverture et fermeture according with $pdst :           */
/*       col_LB +|| col_princ +|| col_RB                                */
/* Fermeture : div > div"#corps"> $ContainerGlobal>                     */
/*                    ouverts dans le Header.php                        */
/* =====================================================================*/

global $pdst, $theme_darkness;

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

        colsyst('#col_RB');

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
            </div>
        </div>';
        break;

    case '6':
        echo '</div>';

        colsyst('#col_LB');

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

// ContainerGlobal permet de transmettre · Theme-Dynamic un élément de personnalisation après
// le chargement de footer.html / Si vide alors rien de plus n'est affiché par TD
$ContainerGlobal = '</div>';

// pilotage du mode dark/light du thème ...
echo '<script type="text/javascript">
        //<![CDATA[
            (() => {
                "use strict"
                const theme = localStorage.setItem("theme", "' . $theme_darkness . '");
                var getStoredTheme = localStorage.getItem("theme");
                if (getStoredTheme === "auto") {
                    document.querySelector("body").setAttribute("data-bs-theme", (window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"))
                } else {
                    document.querySelector("body").setAttribute("data-bs-theme", "' . $theme_darkness . '");
                }
            })()
        //]]>
    </script>';

// Ne supprimez pas cette ligne / Don't remove this line
//require_once 'themes/base/views/footer.php';
// Ne supprimez pas cette ligne / Don't remove this line

global $theme;

$rep = false;

settype($ContainerGlobal, 'string');

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

global $tiny_mce;
if ($tiny_mce) {
    echo Editeur::affEditeur('tiny_mce', 'end');
}

// include externe file from modules/include for functions, codes ...
footer_before();

foot();

// include externe file from modules/themes include for functions, codes ...
if (isset($user)) {
    global $cookie9;
    footer_after($cookie9);
} else {
    global $Default_Theme;
    footer_after($Default_Theme);
}

echo '</body>
</html>';

include 'sitemap.php';

sql_close();
