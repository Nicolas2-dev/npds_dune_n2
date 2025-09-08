<?php

use Npds\Config\Config;
use App\Library\Block\Block;
use App\Library\Access\Access;
use App\Library\Cookie\Cookie;
use App\Library\Session\Session;
use App\Library\String\Sanitize;
use Npds\Support\Facades\Request;
use App\Library\Language\Language;
use App\Library\Metalang\Metalang;
use App\Library\Security\UrlProtector;

/*
|--------------------------------------------------------------------------
| A revoir !
|--------------------------------------------------------------------------
|
*/

if (Config::get('app.debug')) {

    // Report NO ERROR
    // error_reporting(0);

    // Devel report
    // error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

    // standard ERROR report
    //error_reporting(E_ERROR | E_WARNING | E_PARSE);

    // report toutes les erreurs.
    // error_reporting(E_ALL);

    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

/*
|--------------------------------------------------------------------------
| Note en faire une function dans une class. A revoir !
|--------------------------------------------------------------------------
|
*/

// First of all : Spam from IP / |5 indicate that the same IP has passed 6 times with status KO in the anti_spambot function
$path_log = 'storage/logs/spam.log';

if (file_exists($path_log)) {
    $tab_spam = str_replace("\r\n", '', file($path_log));

    if (is_array($tab_spam)) {
        $ipadr = Request::getip();
        $ipv = strstr($ipadr, ':') ? '6' : '4';

        if (in_array($ipadr . '|5', $tab_spam)) {
            Access::accessDenied();
        }

        // nous pouvons bannir une plage d'adresse ip en V4 (dans l'admin IPban sous forme x.x.%|5 ou x.x.x.%|5)
        if ($ipv == '4') {
            $ip4detail = explode('.', $ipadr);

            if (in_array($ip4detail[0] . '.' . $ip4detail[1] . '.%|5', $tab_spam)) {
                Access::accessDenied();
            }

            if (in_array($ip4detail[0] . '.' . $ip4detail[1] . '.' . $ip4detail[2] . '.%|5', $tab_spam)) {
                Access::accessDenied();
            }
        }

        // nous pouvons bannir une plage d'adresse ip en V6 (dans l'admin IPban sous forme x:x:%|5 ou x:x:x:%|5)
        if ($ipv == '6') {
            $ip6detail = explode(':', $ipadr);

            if (in_array($ip6detail[0] . ':' . $ip6detail[1] . ':%|5', $tab_spam)) {
                Access::accessDenied();
            }

            if (in_array($ip6detail[0] . ':' . $ip6detail[1] . ':' . $ip6detail[2] . ':%|5', $tab_spam)) {
                Access::accessDenied();
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| A revoir !
|--------------------------------------------------------------------------
|
*/

// Get values, slash, filter and extract
if (!empty($_GET)) {
    array_walk_recursive($_GET, [Sanitize::class, 'addslashesGpc']);
    reset($_GET); // no need

    array_walk_recursive($_GET, [UrlProtector::class, 'urlProtect']);
    extract($_GET, EXTR_OVERWRITE);
}

/*
|--------------------------------------------------------------------------
| A revoir !
|--------------------------------------------------------------------------
|
*/

if (!empty($_POST)) {
    array_walk_recursive($_POST, [Sanitize::class, 'addslashesGpc']);
    /*
        array_walk_recursive($_POST, [UrlProtector::class, 'post_protect']);

        if(!isset($_SERVER['HTTP_REFERER'])) {
            Log::ecrireLog('security', 'Ghost form in ' . $_SERVER['ORIG_PATH_INFO'] . ' => who playing with form ?', '');
            Spam::logSpambot('', 'false');
            Access::accessDenied();
            
        } else if ($_SERVER['HTTP_REFERER'] !== $nuke_url.$_SERVER['ORIG_PATH_INFO']) {
            Log::ecrireLog('security', 'Ghost form in ' . $_SERVER['ORIG_PATH_INFO'] . '. => ' . $_SERVER['HTTP_REFERER'], '');
            Spam::logSpambot('', "false");
            Access::accessDenied();
        }
        */

    extract($_POST, EXTR_OVERWRITE);
}

/*
|--------------------------------------------------------------------------
| A revoir !
|--------------------------------------------------------------------------
|
*/

// Cookies - analyse et purge - shiney 07-11-2010
if (!empty($_COOKIE)) {
    extract($_COOKIE, EXTR_OVERWRITE);
}

/*
|--------------------------------------------------------------------------
| A revoir !
|--------------------------------------------------------------------------
|
*/

if (isset($user)) {
    $ibid = explode(':', base64_decode($user));
    array_walk($ibid, [UrlProtector::class, 'urlProtect']);
    $user = base64_encode(str_replace('%3A', ':', urlencode(base64_decode($user))));
}

/*
|--------------------------------------------------------------------------
| A revoir !
|--------------------------------------------------------------------------
|
*/

if (isset($user_language)) {
    $ibid = explode(':', $user_language);
    array_walk($ibid, [UrlProtector::class, 'urlProtect']);
    $user_language = str_replace('%3A', ':', urlencode($user_language));
}

/*
|--------------------------------------------------------------------------
| A revoir !
|--------------------------------------------------------------------------
|
*/

if (isset($admin)) {
    $ibid = explode(':', base64_decode($admin));
    array_walk($ibid, [UrlProtector::class, 'urlProtect']);
    $admin = base64_encode(str_replace('%3A', ':', urlencode(base64_decode($admin))));
}

/*
|--------------------------------------------------------------------------
| A revoir !
|--------------------------------------------------------------------------
|
*/

// Cookies - analyse et purge - shiney 07-11-2010
if (!empty($_SERVER)) {
    extract($_SERVER, EXTR_OVERWRITE);
}

/*
|--------------------------------------------------------------------------
| A revoir !
|--------------------------------------------------------------------------
|
*/

if (!empty($_ENV)) {
    extract($_ENV, EXTR_OVERWRITE);
}

/*
|--------------------------------------------------------------------------
| A revoir !
|--------------------------------------------------------------------------
|
*/

if (!empty($_FILES)) {
    foreach ($_FILES as $key => $value) {
        $$key = $value['tmp_name'];
    }
}

/*
|--------------------------------------------------------------------------
| Load language a revoir !
|--------------------------------------------------------------------------
|
*/

include 'language/' . $language . '/lang-' . $language . '.php';

/*
|--------------------------------------------------------------------------
| Database a revoir !
|--------------------------------------------------------------------------
|
*/

include 'library/database/mysqli.php';

$dblink = Mysql_Connexion();

/*
|--------------------------------------------------------------------------
| Deprecated plus de mainfile !
|--------------------------------------------------------------------------
|
*/

//$mainfile = 1; // ==> depredted

/*
|--------------------------------------------------------------------------
| auth a revoir !
|--------------------------------------------------------------------------
|
*/

require_once 'auth.inc.php';

/*
|--------------------------------------------------------------------------
| Cookie user a revoir !
|--------------------------------------------------------------------------
|
*/

if (isset($user)) {
    $cookie = Cookie::cookieDecode($user);
}

/*
|--------------------------------------------------------------------------
| Session Manage.
|--------------------------------------------------------------------------
|
*/

Session::sessionManage();

/*
|--------------------------------------------------------------------------
| Language a revoir !
|--------------------------------------------------------------------------
|
*/

$tab_langue = Language::makeTabLangue();

/*
|--------------------------------------------------------------------------
| Load Metalang glossaire.
|--------------------------------------------------------------------------
|
*/

global $meta_glossaire; // global a supprimer 
$meta_glossaire = Metalang::chargMetalang();

/*
|--------------------------------------------------------------------------
| Charegement des blocks.
|--------------------------------------------------------------------------
|
*/

Block::loadBlocks('blocks');
