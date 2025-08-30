<?php

namespace App\Library\Assets;

use App\Library\Page\PageRef;


class Css
{

    /**
     * Recherche et génère les balises <link> pour inclure les fichiers CSS.
     * 
     * Charge :
     * - le framework CSS (ex: bootstrap)
     * - le CSS principal du thème pour la langue courante
     * - les CSS alternatifs ou spécifiques à l'impression
     * 
     * Le HTML généré utilise des quotes simples pour être compatible avec JavaScript.
     *
     * @param string      $tmp_theme       Nom du thème temporaire
     * @param string      $language        Code langue courant (ex: "fr")
     * @param string      $fw_css          Nom du framework CSS
     * @param string|null $css_pages_ref   Références CSS spécifiques aux pages (optionnel)
     * @param string|null $css             CSS supplémentaire (optionnel)
     *
     * @return string HTML des balises <link> pour les fichiers CSS
     */
    public static function import_css_javascript(
        string $tmp_theme, 
        string $language, 
        string $fw_css, 
        ?string $css_pages_ref = '', 
        ?string $css = ''
    ): string {
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

    /**
     * Fonction identique à import_css_javascript mais retourne du HTML
     * avec des quotes doubles, compatible directement pour l'inclusion dans du code HTML standard.
     *
     * @param string      $tmp_theme       Nom du thème temporaire
     * @param string      $language        Code langue courant (ex: "fr")
     * @param string      $fw_css          Nom du framework CSS
     * @param string|null $css_pages_ref   Références CSS spécifiques aux pages (optionnel)
     * @param string|null $css             CSS supplémentaire (optionnel)
     *
     * @return string HTML des balises <link> avec double quotes
     */
    public static function import_css(
        string $tmp_theme, 
        string $language, 
        string $fw_css, 
        ?string $css_pages_ref = '', 
        ?string $css = ''
    ): string {
        return str_replace("'", "\"", static::import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css));
    }
}
