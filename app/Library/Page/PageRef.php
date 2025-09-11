<?php

namespace App\Library\Page;

// Note : class a finir voir fin de fichier !!!

class PageRef
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
     * Modèles HTML pour l'inclusion des fichiers CSS.
     *
     * - 'theme_css' : modèle pour les fichiers CSS situés dans le dossier du thème actif.
     * - 'tab_css'   : modèle pour les fichiers CSS avec un chemin absolu ou relatif direct.
     *
     * @var array<string, string>
     */
    protected $templates = [
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
    public function importPageRefCss(string $css_pages_ref, string $css): ?string // Bug : $css ne sert a rien puisque écraser plus bas !!!
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

// class a finir 

/*

// LOAD pages.php and Go ...
settype($PAGES, 'array');

global $pdst, $Titlesitename, $PAGES;

require_once 'routing/pages.php';

// import pages.php specif values from theme (toutes valeurs déjà définies dans themes/routing/pages.php seront donc modifiées !)
if (file_exists('themes/' . $tmp_theme . '/routing/pages.php')) {
    include 'themes/' . $tmp_theme . '/routing/pages.php';
}

$page_uri = preg_split('#(&|\?)#', $_SERVER['REQUEST_URI']);
$Npage_uri = count($page_uri);
$pages_ref = basename($page_uri[0]);

if ($pages_ref == 'user.php') {
    $pages_ref = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], 'user.php'));
}

// Static page and Module can have Bloc, Title ....
if ($pages_ref == 'static.php') {
    $pages_ref = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], 'static.php'));
}

if ($pages_ref == 'modules.php') {
    $pages_ref = (isset($PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=' . $ModStart . '*']['title']))
        ? 'modules.php?ModPath=' . $ModPath . '&ModStart=' . $ModStart . '*'
        : substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], 'modules.php'));
}

// Admin function can have all the PAGES attributs except Title
if ($pages_ref == 'admin.php') {
    if (array_key_exists(1, $page_uri)) {
        if (array_key_exists($pages_ref . '?' . $page_uri[1], $PAGES)) {
            if (array_key_exists('title', $PAGES[$pages_ref . '?' . $page_uri[1]])) {
                $pages_ref .= '?' . $page_uri[1];
            }
        }
    }
}
*/


/*
// desativer base npds
if ($pages_ref == 'admin.php') {
    $others = '';

    if (array_key_exists(1, $page_uri)) {
        foreach($page_uri as $k => $partofurl) {
            if ($k == 1) {
               $firstPara = '?'.$page_uri[$k];
            }

            if($k>1) {
               $others .= '&'.$page_uri[$k];
            }
        }

        $pages_ref .= $firstPara.$others;
    }
}
*/

/*
// extend usage of pages.php : blocking script with part of URI for user, admin or with the value of a VAR
if ($Npage_uri > 1) {
    for ($uri = 1; $uri < $Npage_uri; $uri++) {
        if (array_key_exists($page_uri[$uri], $PAGES)) {
            if (!$$PAGES[$page_uri[$uri]]['run']) {
                header('location: ' . $PAGES[$page_uri[$uri]]['title']);
                die();
            }
        }
    }
}

// -----------------------
// A partir de ce niveau - $PAGES[$pages_ref] doit exister - sinon c'est que la page n'est pas dans pages.php
// -----------------------
if (array_key_exists($pages_ref, $PAGES)) {

    // what a bloc ... left, right, both, ...
    if (array_key_exists('blocs', $PAGES[$pages_ref])) {
        $pdst = $PAGES[$pages_ref]['blocs'];
    }

    // block execution of page with run attribute = no
    if ($PAGES[$pages_ref]['run'] == 'no') {
        if ($pages_ref == 'index.php') {
            $Titlesitename = 'NPDS';

            if (file_exists('storage/meta/meta.php')) {
                include('storage/meta/meta.php');
            }

            if (file_exists('storage/static/webclosed.txt')) {
                include('storage/static/webclosed.txt');
            }

            die();
        } else {
            header('location: index.php');
            die();
        }

        // run script to another 'location'
    } elseif (($PAGES[$pages_ref]['run'] != 'yes') and (($PAGES[$pages_ref]['run'] != ''))) {
        header('location: ' . $PAGES[$pages_ref]['run']);
    }

    // Assure la gestion des titres ALTERNATIFS
    $tab_page_ref = explode('|', $PAGES[$pages_ref]['title']);

    if (count($tab_page_ref) > 1) {
        $PAGES[$pages_ref]['title'] = (strlen($tab_page_ref[1]) > 1) ? $tab_page_ref[1] : $tab_page_ref[0];
        $PAGES[$pages_ref]['title'] = strip_tags($PAGES[$pages_ref]['title']);
    }

    $fin_title = substr($PAGES[$pages_ref]['title'], -1);
    $TitlesitenameX = Language::affLangue(substr($PAGES[$pages_ref]['title'], 0, strlen($PAGES[$pages_ref]['title']) - 1));

    if ($fin_title == '+') {
        $Titlesitename = $TitlesitenameX . ' - ' . $Titlesitename;
    } else if ($fin_title == '-') {
        $Titlesitename = $TitlesitenameX;
    }

    if ($Titlesitename == '') {
        $Titlesitename = $sitename;
    }

    // globalisation de la variable title pour marquetapage mais protection pour la zone admin
    if ($pages_ref != 'admin.php') {
        global $title;
    }

    if (!$title) {
        $title = ($fin_title == '+' or $fin_title == '-')
            ? $TitlesitenameX
            : Language::affLangue(substr($PAGES[$pages_ref]['title'], 0, strlen($PAGES[$pages_ref]['title'])));
    } else {
        $title = Hack::removeHack($title);
    }

    // meta description
    settype($m_description, 'string');

    if (array_key_exists('meta-description', $PAGES[$pages_ref]) and ($m_description == '')) {
        $m_description = Language::affLangue($PAGES[$pages_ref]['meta-description']);
    }

    // meta keywords
    settype($m_keywords, 'string');

    if (array_key_exists('meta-keywords', $PAGES[$pages_ref]) and ($m_keywords == '')) {
        $m_keywords = Language::affLangue($PAGES[$pages_ref]['meta-keywords']);
    }
}

// Initialisation de TinyMCE
global $tiny_mce, $tiny_mce_theme, $tiny_mce_relurl;
if ($tiny_mce) {
    if (array_key_exists($pages_ref, $PAGES)) {
        if (array_key_exists('TinyMce', $PAGES[$pages_ref])) {
            $tiny_mce_init = true;

            if (array_key_exists('TinyMce-theme', $PAGES[$pages_ref])) {
                $tiny_mce_theme = $PAGES[$pages_ref]['TinyMce-theme'];
            }

            if (array_key_exists('TinyMceRelurl', $PAGES[$pages_ref])) {
                $tiny_mce_relurl = $PAGES[$pages_ref]['TinyMceRelurl'];
            }
        } else {
            $tiny_mce_init = false;
            // $tiny_mce=false; //pourquoi la redéfinir - cela affecte le controle de son état dans les préférences
        }
    } else {
        $tiny_mce_init = false;
        // $tiny_mce=false;// idem sup
    }
} else {
    $tiny_mce_init = false;
}

// Chargeur de CSS via PAGES.PHP 

// !!! Note : ici bug sur css qui et envoyer sur head qui lui renvoie sur Css::importCss() qui renvoie sur Css::importCssJavascript() 
// et $css fini par etre ecraser par  $oups = $PAGES[$css_pages_ref]['css'];  ==> $css = substr($oups, 0, -1);

if (array_key_exists($pages_ref, $PAGES)) {
    if (array_key_exists('css', $PAGES[$pages_ref])) {
        $css_pages_ref = $pages_ref;
        $css = $PAGES[$pages_ref]['css'];
    } else {
        $css_pages_ref = '';
        $css = '';
    }
} else {
    $css_pages_ref = '';
    $css = '';
}

// Mod by Jireck - Chargeur de JS via PAGES.PHP
if (array_key_exists($pages_ref, $PAGES)) {
    if (array_key_exists('js', $PAGES[$pages_ref])) {
        $js = $PAGES[$pages_ref]['js'];

        if ($js != '') {
            global $pages_js;

            $pages_js = $js;
        }
    } else {
        $js = '';
    }
} else {
    $js = '';
}

*/


// code a la base dans header.php function head()
/*
        if ($js) {
            if (is_array($js)) {
                foreach ($js as $k => $tab_js) {
                    if (stristr($tab_js, 'http://') || stristr($tab_js, 'https://')) {
                        echo '<script type="text/javascript" src="' . $tab_js . '"></script>';
                    } else {
                        if (file_exists('themes/' . $tmp_theme . '/assets/js/' . $tab_js) and ($tab_js != '')) {
                            echo '<script type="text/javascript" src="themes/' . $tmp_theme . '/assets/js/' . $tab_js . '"></script>';
                        } elseif (file_exists("$tab_js") and ($tab_js != "")) {
                            echo '<script type="text/javascript" src="' . $tab_js . '"></script>';
                        }
                    }
                }
            } else {
                if (file_exists('themes/' . $tmp_theme . '/assets/js/' . $js)) {
                    echo '<script type="text/javascript" src="themes/' . $tmp_theme . '/assets/js/' . $js . '"></script>';
                } elseif (file_exists($js)) {
                    echo '<script type="text/javascript" src="' . $js . '"></script>';
                }
            }
        }
*/