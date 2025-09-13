<?php

namespace Npds\Support\Facades;

use BadMethodCallException;
use Npds\View\ViewBootstrap;


class View
{

    /**
     * Redirige les appels statiques vers l'instance de Factory.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        $factory = ViewBootstrap::getInstance()->getFactory();

        if (!method_exists($factory, $method)) {
            throw new BadMethodCallException("Erreur : la méthode [$method] appelée sur la Factory de vues n'existe pas.");
        }

        return $factory->$method(...$parameters);
    }
}
