<?php

namespace App\Library\Http;


class Request
{

    /**
     * Récupère l'adresse IP réelle du client.
     *
     * Tente de déterminer l'IP en fonction des en-têtes HTTP et des variables serveur.
     *
     * @return string Adresse IP du client
     */
    public static function getip(): string
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        if (strpos($realip, ',') > 0) {
            $realip = substr($realip, 0, strpos($realip, ',') - 1);
        }

        // from Gu1ll4um3r0m41n - 08-05-2007 - dev 2012
        return trim($realip);
    }

}
