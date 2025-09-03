<?php

namespace App\Library\Security;

use App\Library\Access\Access;


class UrlProtector
{

    /**
     * Vérifie et protège les URL contre les contenus ou clés interdites.
     *
     * Cette fonction :
     * - Inclut la configuration `urlProtect.php` contenant les mots clés et contenus interdits
     * - Décode l'URL passée et effectue des comparaisons avec les valeurs interdites
     * - Bloque l'accès si une correspondance est trouvée en appelant `Access::accessDenied()`
     *
     * @param string $arr La valeur de l'URL à vérifier (ex: `$_GET['param']`)
     * @param string $key La clé associée dans l'URL (ex: `'param'`)
     *
     * @return void
     */
    public static function urlProtect(string $arr, string $key): void
    {
        // include urlProtect Bad Words and create the filter function
        include 'config/urlProtect.php';

        // mieux faire face aux techniques d'évasion de code : base64_decode(utf8_decode(bin2hex($arr))));
        $arr = rawurldecode($arr);
        $RQ_tmp = strtolower($arr);
        $RQ_tmp_large = strtolower($key) . '=' . $RQ_tmp;

        if (
            in_array($RQ_tmp, $bad_uri_content)
            or
            in_array($RQ_tmp_large, $bad_uri_content)
            or
            in_array($key, $bad_uri_key, true)
            or
            count($badname_in_uri) > 0
        ) {
            unset($bad_uri_content);
            unset($bad_uri_key);
            unset($badname_in_uri);

            Access::accessDenied();
        }
    }
}
