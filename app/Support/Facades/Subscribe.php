<?php

namespace App\Support\Facades;

use App\Library\Subscribe\Subscribe as SubscribeManager;


class Subscribe
{

    /**
     * Appelle une méthode statique de l'instance de Theme.
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
        $instance = SubscribeManager::getInstance();

        return call_user_func_array(array($instance, $method), $parameters);
    }
}
