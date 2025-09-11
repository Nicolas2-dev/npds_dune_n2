<?php

namespace App\Library\Routing;

// Note Deprecated a venir !!

class Url
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
     * Redirige vers une URL donnée en utilisant JavaScript.
     *
     * Cette méthode génère un script JavaScript qui change la location du document.
     * Utile lorsque l'utilisation de `header('Location: ...')` n'est pas possible.
     *
     * @param string $urlx L'URL vers laquelle rediriger.
     * @return void
     */
    public function redirectUrl(string $urlx): void
    {
        echo "<script type=\"text/javascript\">\n";
        echo "//<![CDATA[\n";
        echo "document.location.href='" . $urlx . "';\n";
        echo "//]]>\n";
        echo "</script>";
    }
}
