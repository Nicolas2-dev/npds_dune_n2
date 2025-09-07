<?php

namespace Npds\Support\Facades;

use Npds\Events\Dispatcher;

class Event
{

    /**
     * Appelle une méthode statique de l'instance du Dispatcher.
     *
     * Cette méthode magique permet de rediriger tous les appels statiques vers
     * l'instance unique de Dispatcher.
     *
     * @param string $method      Le nom de la méthode appelée statiquement.
     * @param array  $parameters  Les paramètres passés à la méthode.
     *
     * @return mixed  Le résultat de l'appel de la méthode sur Dispatcher.
     */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        $instance = Dispatcher::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
    
}
