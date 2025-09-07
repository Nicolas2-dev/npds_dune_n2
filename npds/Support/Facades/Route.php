<?php

namespace Npds\Support\Facades;

use Npds\Routing\Router;

class Route
{

    /**
     * Appelle dynamiquement une méthode statique sur l'instance du routeur.
     *
     * Cette méthode redirige les appels statiques vers l'instance unique de Router.
     *
     * @param string $method     Le nom de la méthode appelée.
     * @param array  $parameters Les paramètres passés à la méthode.
     *
     * @return mixed Le résultat de l'appel de la méthode sur l'instance de Router.
     */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        $instance = Router::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }

}
