<?php

namespace App\Support\Security;

use Npds\Config\Config;
use App\Support\Facades\Access;

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
        // mieux faire face aux techniques d'évasion de code 
        // : base64_decode(utf8_decode(bin2hex($arr))));
        $arr            = rawurldecode($arr);
        $RQ_tmp         = strtolower($arr);
        $RQ_tmp_large   = strtolower($key) . '=' . $RQ_tmp;

        $bad_uri_content = Config::get('protect.filters');    

        $bad_uri_key     = static::getServerKeys();
        $badname_in_uri  = static::detectForbiddenInUri();

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

    /**
     * Récupère toutes les clés de l'environnement $_SERVER.
     *
     * @return array
     */
    public static function getServerKeys(): array
    {
        return array_keys($_SERVER);
    }

    /**
     * Retourne la liste des noms de variables interdites dans l'URI.
     *
     * @return array
     */
    public static function getForbiddenUriNames(): array
    {
        return [
            'GLOBALS',
            '_SERVER',
            '_REQUEST',
            '_GET',
            '_POST',
            '_FILES',
            '_ENV',
            '_COOKIE',
            '_SESSION',
        ];
    }

    /**
     * Vérifie si des noms interdits sont présents dans les paramètres GET.
     *
     * @return array Les noms interdits trouvés dans l'URI
     */
    public static function detectForbiddenInUri(): array
    {
        $forbidden = self::getForbiddenUriNames();
        $getKeys = array_keys($_GET);

        return array_intersect($getKeys, $forbidden);
    }

}
