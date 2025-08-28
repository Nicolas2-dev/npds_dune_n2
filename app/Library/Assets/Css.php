<?php

namespace App\Library\Assets;

use App\Library\Page\PageRef;


class Css
{

    #autodoc import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css) : recherche et affiche la CSS (site, langue courante ou par défaut) / Charge la CSS complémentaire / le HTML ne contient que de simple quote pour être compatible avec javascript
    public static function import_css_javascript(
        string $tmp_theme, 
        string $language, 
        string $fw_css, 
        ?string $css_pages_ref = '', 
        ?string $css = '')
    {
        $tmp = '';

        // CSS framework
        if (file_exists('../assets/skins/' . $fw_css . '/bootstrap.min.css')) {
            $tmp .= '<link href="../assets/skins/' . $fw_css . '/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />';
        }

        // CSS standard 
        if (file_exists('../themes/' . $tmp_theme . '/assets/css/' . $language . '-style.css')) {
            $tmp .= '<link href="../themes/' . $tmp_theme . '/assets/css/' . $language . '-style.css" title="default" rel="stylesheet" type="text/css" media="all" />';

            if (file_exists('../themes/' . $tmp_theme . '/assets/css/' . $language . '-style-AA.css')) {
                $tmp .= '<link href="../themes/' . $tmp_theme . '/assets/css/' . $language . '-style-AA.css" title="alternate stylesheet" rel="alternate stylesheet" type="text/css" media="all" />';
            }

            if (file_exists('../themes/' . $tmp_theme . '/assets/css/' . $language . '-print.css')) {
                $tmp .= '<link href="../themes/' . $tmp_theme . '/assets/css/' . $language . '-print.css" rel="stylesheet" type="text/css" media="print" />';
            }
        } elseif (file_exists('../themes/' . $tmp_theme . '/assets/css/style.css')) {
            $tmp .= '<link href="../themes/' . $tmp_theme . '/assets/css/style.css" title="default" rel="stylesheet" type="text/css" media="all" />';

            if (file_exists('../themes/' . $tmp_theme . '"assets/css/style-AA.css')) {
                $tmp .= '<link href="../themes/' . $tmp_theme . '/assets/css/style-AA.css" title="alternate stylesheet" rel="alternate stylesheet" type="text/css" media="all" />';
            }

            if (file_exists('../themes/' . $tmp_theme . '/assets/css/print.css')) {
                $tmp .= '<link href="../themes/' . $tmp_theme . '/assets/css/print.css" rel="stylesheet" type="text/css" media="print" />';
            }
        } else {
            $tmp .= '<link href="../themes/base/assets/css/style.css" title="default" rel="stylesheet" type="text/css" media="all" />';
        }

        //$tmp .= PageRef::import_page_ref_css($css_pages_ref, $css); // note ici voir pour le bug $css

        return $tmp;
    }

    #autodoc import_css($tmp_theme, $language, $fw_css, $css_pages_ref, $css) : Fonctionnement identique à import_css_javascript sauf que le code HTML en retour ne contient que de double quote
    public static function import_css(
        string $tmp_theme, 
        string $language, 
        string $fw_css, 
        ?string $css_pages_ref = '', 
        ?string $css = '')
    {
        return str_replace("'", "\"", static::import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css));
    }
}
