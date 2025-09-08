<?php

namespace App\Library\Url;

// Note Deprecated a venir !!

class Url
{

    /**
     * Redirige vers une URL donnée en utilisant JavaScript.
     *
     * Cette méthode génère un script JavaScript qui change la location du document.
     * Utile lorsque l'utilisation de `header('Location: ...')` n'est pas possible.
     *
     * @param string $urlx L'URL vers laquelle rediriger.
     * @return void
     */
    public static function redirectUrl(string $urlx): void
    {
        echo "<script type=\"text/javascript\">\n";
        echo "//<![CDATA[\n";
        echo "document.location.href='" . $urlx . "';\n";
        echo "//]]>\n";
        echo "</script>";
    }
}
