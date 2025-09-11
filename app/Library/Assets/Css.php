<?php

namespace App\Library\Assets;

use App\Support\Facades\Theme;
use App\Support\Facades\Assets as AssetManager;

class Css
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    
    /**
     * Retourne l'instance singleton du dispatcher.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

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
    public function loadCss(): void {

        global $language;

        $tmp_theme = Theme::getTheme();
        
        // CSS standard 
        if (file_exists('../themes/' . $tmp_theme . '/assets/css/' . $language . '-style.css')) {
            AssetManager::addCss(path: 'themes/' . $tmp_theme . '/assets/css/' . $language . '-style.css');

            if (file_exists('../themes/' . $tmp_theme . '/assets/css/' . $language . '-style-AA.css')) {
                AssetManager::addCss(path: 'themes/' . $tmp_theme . '/assets/css/' . $language . '-style-AA.css');
            }

            if (file_exists('../themes/' . $tmp_theme . '/assets/css/' . $language . '-print.css')) {
                AssetManager::addCss(path: 'themes/' . $tmp_theme . '/assets/css/' . $language . '-print.css');
            }
        } elseif (file_exists('../themes/' . $tmp_theme . '/assets/css/style.css')) {
            AssetManager::addCss(path: 'themes/' . $tmp_theme . '/assets/css/style.css');

            if (file_exists('../themes/' . $tmp_theme . '"assets/css/style-AA.css')) {
                AssetManager::addCss(path: 'themes/' . $tmp_theme . '/assets/css/style-AA.css');
            }

            if (file_exists('../themes/' . $tmp_theme . '/assets/css/print.css')) {
                AssetManager::addCss(path: 'themes/' . $tmp_theme . '/assets/css/print.css');
            }
        } else {
            AssetManager::addCss(path: 'themes/base/assets/css/style.css');
        }
    }

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
    public function importCssJavascript(
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

        //$tmp .= PageRef::importPageRefCss($css_pages_ref, $css); // note ici voir pour le bug $css

        return $tmp;
    }

    /**
     * Fonction identique à importCssJavascript mais retourne du HTML
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
    public function importCss(
        string $tmp_theme,
        string $language,
        string $fw_css,
        ?string $css_pages_ref = '',
        ?string $css = ''
    ): string {
        return str_replace("'", "\"", $this->importCssJavascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css));
    }
}
