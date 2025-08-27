<?php

namespace App\Library\Assets;


class Css
{

    #autodoc import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css) : recherche et affiche la CSS (site, langue courante ou par défaut) / Charge la CSS complémentaire / le HTML ne contient que de simple quote pour être compatible avec javascript
    function import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref = '', $css = '')
    {
        $tmp = '';

        // CSS framework
        if (file_exists('assets/skins/' . $fw_css . '/bootstrap.min.css')) {
            $tmp .= "<link href='assets/skins/" . $fw_css . "/bootstrap.min.css' rel='stylesheet' type='text/css' media='all' />";
        }

        // CSS standard 
        if (file_exists('themes/' . $tmp_theme . '/assets/css/' . $language . '-style.css')) {
            $tmp .= "<link href='themes/$tmp_theme/style/$language-style.css' title='default' rel='stylesheet' type='text/css' media='all' />";

            if (file_exists('themes/' . $tmp_theme . '/assets/css/' . $language . '-style-AA.css')) {
                $tmp .= "<link href='themes/$tmp_theme/assets/css/$language-style-AA.css' title='alternate stylesheet' rel='alternate stylesheet' type='text/css' media='all' />";
            }

            if (file_exists('themes/' . $tmp_theme . '/assets/css/' . $language . '-print.css')) {
                $tmp .= "<link href='themes/$tmp_theme/assets/css/$language-print.css' rel='stylesheet' type='text/css' media='print' />";
            }
        } else if (file_exists('themes/' . $tmp_theme . '/assets/css/style.css')) {
            $tmp .= "<link href='themes/$tmp_theme/assets/css/style.css' title='default' rel='stylesheet' type='text/css' media='all' />";

            if (file_exists('themes/' . $tmp_theme . '"assets/css/style-AA.css')) {
                $tmp .= "<link href='themes/$tmp_theme/assets/css/style-AA.css' title='alternate stylesheet' rel='alternate stylesheet' type='text/css' media='all' />";
            }

            if (file_exists('themes/' . $tmp_theme . '/assets/css/print.css')) {
                $tmp .= "<link href='themes/$tmp_theme/assets/css/print.css' rel='stylesheet' type='text/css' media='print' />";
            }
        } else {
            $tmp .= "<link href='themes/base/assets/css/style.css' title='default' rel='stylesheet' type='text/css' media='all' />";
        }

        // Chargeur CSS spécifique
        if ($css_pages_ref) {

            include 'routing/pages.php';

            if (is_array($PAGES[$css_pages_ref]['css'])) {
                foreach ($PAGES[$css_pages_ref]['css'] as $tab_css) {

                    $admtmp = '';
                    $op = substr($tab_css, -1);

                    if ($op == '+' or $op == '-') {
                        $tab_css = substr($tab_css, 0, -1);
                    }

                    if (stristr($tab_css, 'http://') || stristr($tab_css, 'https://')) {
                        $admtmp = "<link href='$tab_css' rel='stylesheet' type='text/css' media='all' />";

                    } else {
                        if (file_exists('themes/' . $tmp_theme . '/assets/css/' . $tab_css) and ($tab_css != '')) {
                            $admtmp = "<link href='themes/" . $tmp_theme . "/assets/css/" . $tab_css . "' rel='stylesheet' type='text/css' media='all' />";

                        } elseif (file_exists("$tab_css") and ($tab_css != '')) {
                            $admtmp = "<link href='$tab_css' rel='stylesheet' type='text/css' media='all' />";
                        }
                    }

                    if ($op == '-') {
                        $tmp = $admtmp;
                    } else {
                        $tmp .= $admtmp;
                    }
                }
            } else {
                $oups = $PAGES[$css_pages_ref]['css'];

                settype($oups, 'string');

                $op = substr($oups, -1);
                $css = substr($oups, 0, -1);

                if (($css != '') and (file_exists('themes/' . $tmp_theme . '/assets/css/' . $css))) {
                    if ($op == '-') {
                        $tmp = "<link href='themes/" . $tmp_theme . "/assets/css/" . $css . "' rel='stylesheet' type='text/css' media='all' />";
                    } else {
                        $tmp .= "<link href='themes/" . $tmp_theme . "/assets/css/" . $css . "' rel='stylesheet' type='text/css' media='all' />";
                    }
                }
            }
        }

        return $tmp;
    }

    #autodoc import_css($tmp_theme, $language, $fw_css, $css_pages_ref, $css) : Fonctionnement identique à import_css_javascript sauf que le code HTML en retour ne contient que de double quote
    function import_css($tmp_theme, $language, $fw_css, $css_pages_ref, $css)
    {
        return str_replace("'", "\"", import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css));
    }
    
}
