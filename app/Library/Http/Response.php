<?php

namespace App\Library\Http;


class Response
{

    /**
     * Vérifie si une URL répond avec un code HTTP spécifique.
     *
     * Note : Fonction basique, peut ne pas fonctionner correctement avec HTTPS.
     *
     * @param string $url URL à vérifier
     * @param int $response_code Code HTTP attendu (par défaut 200)
     * @return bool Retourne true si l'URL renvoie le code attendu, false sinon
     */
    public static function fileContentsExist(string $url, int $response_code = 200): bool
    {
        $headers = get_headers($url);

        if (substr($headers[0], 9, 3) == $response_code) {
            return true;
        } else {
            return false;
        }
    }
}
