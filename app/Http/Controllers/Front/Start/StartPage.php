<?php

namespace App\Http\Controllers\Front\Start;

use Npds\View\View;
use Npds\Config\Config;
use App\Library\Auth\Auth;
use App\Library\News\News;
use App\Library\Edito\Edito;
use Npds\Support\Facades\Redirect;
use Npds\Http\Response as HttpResponse;
use App\Http\Controllers\BaseController;

class StartPage extends BaseController
{

    /**
     * Liste des urls autorisées pour la page d'accueil.
     *
     * @var [type]
     */
    private const ALLOWED_OP = [
        'index',
        'newcategory', 
        'newtopic', 
        'newindex',  
        'edito', 
        'edito-nonews',
        'edito-newindex'
    ];


    /**
     * Constructeur du contrôleur Home
     *
     * Permet d'initialiser le contrôleur, charger des services ou des middleware si nécessaire.
     */
    public function __construct()
    {
        //
    }

    /**
     * Affiche la page d'accueil.
     *
     * Si le paramètre `$start` correspond à une opération autorisée,
     * la méthode renvoie la vue de l’index. 
     * Sinon, elle redirige vers la page de démarrage configurée.
     *
     * @param string|null $start  Segment d’URL optionnel indiquant la section à afficher.
     *
     * @return View|HttpResponse  Vue de la page d’accueil ou réponse de redirection.
     */
    public function index(?string $start = null): View|HttpResponse
    {
        $Start_Page = Config::get('app.Start_Page');

        if (!Auth::AutoReg()) {
            global $user;
        
            unset($user);
        }

        $start = $start ? rtrim($start, '/') : '';

        if (in_array($start, self::ALLOWED_OP, true)) {
            return $this->theindex($start);
        } else {
            return Redirect::to($Start_Page);
        }
    }

    /**
     * Génère la vue principale de l’index.
     *
     * @param string   $start    Point d’entrée ou identifiant de la section à afficher.
     * @param int|null $catid    Identifiant optionnel de catégorie (par défaut 0).
     * @param int|null $marqeur  Identifiant optionnel de marqueur (par défaut 0).
     *
     * @return View   Instance de la vue générée pour l’index.
     */
    private function theindex(string $start, ?int $catid = 0, ?int $marqeur = 0): View
    {
        global $theme, $user;

        $Default_Theme = Config::get('theme.Default_Theme');
        $Default_Skin = Config::get('theme.Default_Skin');

        if (isset($user) and $user != '') {

            global $cookie;
            if ($cookie[9] != '') {
                $ibix = explode('+', urldecode($cookie[9]));

                if (array_key_exists(0, $ibix)) {
                    $theme = $ibix[0];
                } else {
                    $theme = $Default_Theme;
                }

                if (array_key_exists(1, $ibix)) {
                    $skin = $ibix[1];
                } else {
                    $skin = $Default_Skin;
                }

                $tmp_theme = $theme;

                if (!$file = @opendir('themes/' . $theme)) {
                    $tmp_theme = $Default_Theme;
                }
            } else {
                $tmp_theme = $Default_Theme;
            }
        } else {
            $theme = $Default_Theme;
            $skin = $Default_Skin;
            $tmp_theme = $theme;
        }

        $theme = $theme;

        News::automatedNews();

        $renderContent = function () use ($start, $catid, $marqeur, $theme) {
            ob_start();

            if (in_array($start, ['newcategory', 'newtopic', 'newindex', 'edito-newindex'])) {
                News::affNews($start, $catid, $marqeur);
            } else {
                if (file_exists(theme_path($theme . '/central.php'))) {
                    include theme_path($theme . '/central.php');
                } else {
                    if (in_array($start, ['edito', 'edito-nonews'])) {
                      Edito::affEdito();
                    }

                    if ($start != 'edito-nonews') {
                       News::affNews($start, $catid, $marqeur);
                    }
                }
            }

            return ob_get_clean();
        };

        return $this->createView(['content' => $renderContent()], 'theindex')
            ->shares('title', 'Homepage')
            ->with('contentw', 'test with')
            ->with('Start_Page', Config::get('app.Start_Page'));
    }
}

/*
// voir ou je place cette function check install !
// Modification pour IZ-Xinstall - EBH - JPB & PHR
if (file_exists('IZ-Xinstall.ok')) {
    if (file_exists('install.php') || is_dir('install')) {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title>NPDS IZ-Xinstall - Installation Configuration</title>
            </head>
            <body>
                <div style="text-align: center; font-size: 20px; font-family: Arial; font-weight: bold; color: #000000"><br />
                    NPDS IZ-Xinstall - Installation &amp; Configuration
                </div>
                <div style="text-align: center; font-size: 20px; font-family: Arial; font-weight: bold; color: #ff0000"><br />
                    Vous devez supprimer le r&eacute;pertoire "install" ET le fichier "install.php" avant de poursuivre !<br />
                    You must remove the directory "install" as well as the file "install.php" before continuing!
                </div>
            </body>
        </html>';
        die();
    }
} else {
    if (file_exists('install.php') && is_dir('install')) {
        header('location: install.php');
    }
}

// deprecated !
if (!function_exists('Mysql_Connexion')) {
    include 'mainfile.php';
}

// controller !
// Redirect for default Start Page of the portal - look at Admin Preferences for choice
function select_start_page($op)
{
    global $Start_Page, $index;

    if (!Auth::autoReg()) {
        global $user;
        unset($user);
    }

    if (($Start_Page == '')
        || ($op == 'index.php')
        || ($op == 'edito')
        || ($op == 'edito-nonews')
    ) {
        $index = 1;

        theindex($op, '', '');
        die();
    } else {
        Header('Location: ' . $Start_Page);
    }
}

// controller !
function theindex($op, $catid, $marqeur)
{
    include 'header.php';

    // Include cache manager
    global $SuperCache;
    if ($SuperCache) {
        $cache_obj = new SuperCacheManager();
        $cache_obj->startCachingPage();
    } else {
        $cache_obj = new SuperCacheEmpty();
    }

    if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {

        // Appel de la publication de News et la purge automatique
        automatednews();

        global $theme;
        if (($op == 'newcategory')
            || ($op == 'newtopic')
            || ($op == 'newindex')
            || ($op == 'edito-newindex')
        ) {
            aff_news($op, $catid, $marqeur);
        } else {
            if (file_exists('themes/' . $theme . '/views/central.php')) {
                include 'themes/' . $theme . '/Views/central.php';
            } else {
                if (($op == 'edito') || ($op == 'edito-nonews')) {
                    aff_edito();
                }

                if ($op != 'edito-nonews') {
                    aff_news($op, $catid, $marqeur);
                }
            }
        }
    }

    if ($SuperCache) {
        $cache_obj->endCachingPage();
    }

    include 'footer.php';
}

// deprecated !
settype($op, 'string');
settype($catid, 'integer');
settype($marqeur, 'integer');

switch ($op) {

    case 'newindex':
    case 'edito-newindex':
    case 'newcategory':
        theindex($op, $catid, $marqeur);
        break;

    case 'newtopic':
        theindex($op, $topic, $marqeur);
        break;

    default:
        select_start_page($op, '');
        break;
}

*/
