<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/*                                                                      */
/* NPDS Copyright (c) 2001-2024 by Philippe Brunier                     */
/* =========================                                            */
/*                                                                      */
/* Based on phpmyadmin.net  grabber library                             */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/
/* This library grabs the names and values of the variables sent or     */
/* posted to a script in superglobals arrays and sets simple globals    */
/* variables from them                                                  */
/************************************************************************/

use App\Library\Log\Log;
use App\Library\String\Sanitize;
use App\Library\Spam\Spam;
use App\Library\Http\Request;
use App\Library\Access\Access;
use App\Library\Security\UrlProtector;

if (stristr($_SERVER['PHP_SELF'], 'grab_globals.php') and strlen($_SERVER['QUERY_STRING']) != '') {
    include 'admin/die.php';
}

if (!defined('NPDS_GRAB_GLOBALS_INCLUDED')) {
    define('NPDS_GRAB_GLOBALS_INCLUDED', 1);

    // Modify the report level of PHP

    // Report NO ERROR
    // error_reporting(0);

    // Devel report
    // error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

    // standard ERROR report
    //error_reporting(E_ERROR | E_WARNING | E_PARSE);

    // report toutes les erreurs.
    // error_reporting(E_ALL);

    $debug = false;

    if ($debug) {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }

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


    // Get values, slash, filter and extract
    if (!empty($_GET)) {
        array_walk_recursive($_GET, [Sanitize::class, 'addslashesGpc']);
        reset($_GET); // no need

        array_walk_recursive($_GET, [UrlProtector::class, 'urlProtect']);
        extract($_GET, EXTR_OVERWRITE);
    }

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

    // Cookies - analyse et purge - shiney 07-11-2010
    if (!empty($_COOKIE)) {
        extract($_COOKIE, EXTR_OVERWRITE);
    }

    if (isset($user)) {
        $ibid = explode(':', base64_decode($user));
        array_walk($ibid, [UrlProtector::class, 'urlProtect']);
        $user = base64_encode(str_replace('%3A', ':', urlencode(base64_decode($user))));
    }

    if (isset($user_language)) {
        $ibid = explode(':', $user_language);
        array_walk($ibid, [UrlProtector::class, 'urlProtect']);
        $user_language = str_replace('%3A', ':', urlencode($user_language));
    }

    if (isset($admin)) {
        $ibid = explode(':', base64_decode($admin));
        array_walk($ibid, [UrlProtector::class, 'urlProtect']);
        $admin = base64_encode(str_replace('%3A', ':', urlencode(base64_decode($admin))));
    }

    // Cookies - analyse et purge - shiney 07-11-2010
    if (!empty($_SERVER)) {
        extract($_SERVER, EXTR_OVERWRITE);
    }

    if (!empty($_ENV)) {
        extract($_ENV, EXTR_OVERWRITE);
    }

    if (!empty($_FILES)) {
        foreach ($_FILES as $key => $value) {
            $$key = $value['tmp_name'];
        }
    }
}
