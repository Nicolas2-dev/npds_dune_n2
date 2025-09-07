<?php

namespace Npds\Support\Facades;

use Npds\Http\Request as HttpRequest;

class Request
{

    /**
     * Appelle dynamiquement une méthode statique sur l'instance de HttpRequest.
     *
     * Cette méthode magique permet de rediriger tous les appels statiques
     * vers l'instance singleton de HttpRequest.
     *
     * @param string $method     Le nom de la méthode appelée.
     * @param array  $parameters Les paramètres passés à la méthode.
     *
     * @return mixed Le résultat de l'appel de la méthode sur HttpRequest.
     */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        $instance = HttpRequest::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }

}
