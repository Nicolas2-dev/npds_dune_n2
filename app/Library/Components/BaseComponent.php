<?php

namespace App\Library\Components;


abstract class BaseComponent
{

    /**
     * Rend le composant.
     *
     * Cette méthode doit être implémentée par chaque composant.
     *
     * @param array $params Paramètres optionnels passés au composant.
     *
     * @return string|float Le HTML ou la sortie du composant.
     */
    abstract public function render(array $params = []): string|float;

    /**
     * Récupère une valeur de configuration.
     *
     * @param string $key La clé de configuration (ex. 'theme.footer.foot1').
     * @param mixed $default Valeur par défaut si la clé n'existe pas.
     *
     * @return mixed La valeur de configuration.
     */
    protected function config(string $key, mixed $default = null): mixed
    {
        return config($key, $default);
    }

    /**
     * Échappe une chaîne pour l'affichage HTML.
     *
     * @param string $data La donnée à échapper.
     *
     * @return string La donnée échappée.
     */
    protected function escape(string $data): string
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
}
