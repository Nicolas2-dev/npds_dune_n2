<?php

namespace App\Library\Page;


class PageRef
{

    /**
     * Modèles HTML pour l'inclusion des fichiers CSS.
     *
     * - 'theme_css' : modèle pour les fichiers CSS situés dans le dossier du thème actif.
     * - 'tab_css'   : modèle pour les fichiers CSS avec un chemin absolu ou relatif direct.
     *
     * @var array<string, string>
     */
    protected static $templates = [
        'theme_css' => '<link href="themes/%s/assets/css/%s" rel="stylesheet" type="text/css" media="all" />',
        'tab_css'   => '<link href="%s" rel="stylesheet" type="text/css" media="all" />',
    ];


    /**
     * Charge les fichiers CSS spécifiques à une page donnée.
     *
     * Cette méthode inclut les fichiers CSS définis dans le tableau $PAGES pour
     * la page référencée par $css_pages_ref. Les URLs absolues sont directement
     * utilisées, tandis que les fichiers locaux sont recherchés dans le thème actif
     * ou à l'emplacement fourni.
     *
     * @param string $css_pages_ref Référence de la page dont on veut charger le CSS
     * @param string $css           Ce paramètre est écrasé dans le code et n'est pas utilisé
     *
     * @return string|null Le bloc HTML <link> pour inclure les CSS, ou null si aucun CSS trouvé
     */
    public static function importPageRefCss(string $css_pages_ref, string $css): ?string // Bug : $css ne sert a rien puisque écraser plus bas !!!
    {
        // Chargeur CSS spécifique
        if ($css_pages_ref) {

            include 'routing/pages.php';

            $tmp = '';

            if (is_array($PAGES[$css_pages_ref]['css'])) {
                foreach ($PAGES[$css_pages_ref]['css'] as $tab_css) {

                    $admtmp = '';

                    $op = substr($tab_css, -1);

                    if ($op == '+' or $op == '-') {
                        $tab_css = substr($tab_css, 0, -1);
                    }

                    if (stristr($tab_css, 'http://') || stristr($tab_css, 'https://')) {
                        $admtmp = sprintf(static::$templates['tab_css'], $tab_css);
                    } else {
                        if (file_exists('themes/' . $tmp_theme . '/assets/css/' . $tab_css) and ($tab_css != '')) {
                            $admtmp = sprintf(static::$templates['theme_css'], $tmp_theme, $tab_css);
                        } elseif (file_exists($tab_css) and ($tab_css != '')) {
                            $admtmp = sprintf(static::$templates['tab_css'], $tab_css);
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
                $css = substr($oups, 0, -1); // Bug : écrasement de la variable $css !!!

                if (($css != '') and (file_exists('themes/' . $tmp_theme . '/assets/css/' . $css))) {
                    if ($op == '-') {
                        $tmp = sprintf(static::$templates['theme_css'], $tmp_theme, $css);
                    } else {
                        $tmp .= sprintf(static::$templates['theme_css'], $tmp_theme, $css);
                    }
                }
            }

            return $tmp;
        }

        return null;
    }
}
