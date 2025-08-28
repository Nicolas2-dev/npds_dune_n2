<?php

namespace App\Library\Page;


class PageRef
{

    protected static $templates = [
        'theme_css' => '<link href="themes/%s/assets/css/%s" rel="stylesheet" type="text/css" media="all" />',
        'tab_css'   => '<link href="%s" rel="stylesheet" type="text/css" media="all" />',
    ];


    /**
     * 
     *
     * @param   string  $css_pages_ref  [$css_pages_ref description]
     * @param   string  $css            cette argument ne sert a rien ou c'est un bug car dans le code du dessous $css et ecraser par $css = substr($oups, 0, -1);!!!
     *
     * @return  [type]                  [return description]
     */
    public static function import_page_ref_css(string $css_pages_ref, string $css) // Bug : $css ne sert a rien puisque écraser plus bas !!!
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
    }
}
