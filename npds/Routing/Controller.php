<?php

namespace Npds\Routing;

use BadMethodCallException;

class Controller
{

    /**
     * Appelle une méthode du contrôleur avec les paramètres spécifiés.
     *
     * @param string $method      Nom de la méthode à appeler.
     * @param array  $parameters  Tableau des paramètres à passer à la méthode.
     *
     * @return mixed              Retour de la méthode appelée.
     */
    //public function callAction(string $method, array $parameters = []): mixed
    //{
    //    return call_user_func_array(array($this, $method), $parameters);
    //}

    /**
     * Gérez les appels aux méthodes manquantes sur le contrôleur.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException("Method [$method] does not exist.");
    }

}
