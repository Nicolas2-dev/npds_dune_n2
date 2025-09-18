<?php

use Npds\Config\Config;
use App\Support\Sanitize;
use App\Support\Facades\Sql;
use App\Support\Facades\Spam;
use App\Support\Facades\Block;
use App\Support\Facades\Theme;
use App\Support\Facades\Cookie;
use App\Support\Facades\Language;
use App\Support\Facades\Metalang;
use App\Support\Security\UrlProtector;
use App\Library\Language\Sigleton\LanguageManager;


/*
|--------------------------------------------------------------------------
| Mode Debug Interne. A revoir ! va être déprécié !
|--------------------------------------------------------------------------
|
*/

//if (Config::get('debug.debug')) {
//    Debug::initDebug();
//}

/*
|--------------------------------------------------------------------------
| Vérifie si l'adresse IP actuelle est bannie dans le log anti-spam.
|--------------------------------------------------------------------------
|
*/

Spam::checkIP('logs/spam.log', 5);

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
| A revoir ! va être déprécié !
|--------------------------------------------------------------------------
|
*/

// Cookies - analyse et purge - shiney 07-11-2010
if (!empty($_COOKIE)) {
    extract($_COOKIE, EXTR_OVERWRITE);
}

/*
|--------------------------------------------------------------------------
| A revoir ! va être déprécié !
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
| A revoir ! va être déprécié !
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
| A revoir ! va être déprécié !
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
| A revoir ! va être déprécié !
|--------------------------------------------------------------------------
|
*/

// Cookies - analyse et purge - shiney 07-11-2010
if (!empty($_SERVER)) {
    extract($_SERVER, EXTR_OVERWRITE);
}

/*
|--------------------------------------------------------------------------
| A revoir ! va être déprécié !
|--------------------------------------------------------------------------
|
*/

if (!empty($_ENV)) {
    extract($_ENV, EXTR_OVERWRITE);
}

/*
|--------------------------------------------------------------------------
| A revoir ! va être déprécié !
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
| Compression Gzip
|--------------------------------------------------------------------------
|
| Active la compression Gzip si elle est autorisée dans la config
| et que l’environnement le permet.
|
*/

// Compression Gzip (si dispo et pas déjà activée)
if (Config::get('app.gzhandler') === 1 && !headers_sent() && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
}

/*
|--------------------------------------------------------------------------
| Load language a revoir ! va être déprécié !
|--------------------------------------------------------------------------
|
*/

$langManager = LanguageManager::getInstance();

$iso = strtoupper($langManager->getIso($language = Config::get('language.language')));

include APPPATH . 'Language/' . $iso . '/lang-' . $language . '.php';
include APPPATH . 'Language/' . $iso . '/lang-adm-'.$language.'.php';

/*
|--------------------------------------------------------------------------
| Initilisation databasse connection.
|--------------------------------------------------------------------------
|
*/

Sql::getInstance();

/*
|--------------------------------------------------------------------------
| auth a revoir ! Deprecated !
|--------------------------------------------------------------------------
|
*/

// require_once APPPATH .'Http/Controllers/Front/deprecated/auth.inc.php';

/*
|--------------------------------------------------------------------------
| Cookie user a revoir ! va être déprécié !
|--------------------------------------------------------------------------
|
*/

if (isset($user)) {
    $cookie = Cookie::cookieDecode($user);
}

/*
|--------------------------------------------------------------------------
| Load Config Theme.
|--------------------------------------------------------------------------
|
*/

Theme::LoadConfig();

dump(Config::All());

/*
|--------------------------------------------------------------------------
| Session Manage.
|--------------------------------------------------------------------------
|
*/

//Session::sessionManage();

/*
|--------------------------------------------------------------------------
| Language a revoir ! va être déprécié !
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

Block::loadBlocks();
