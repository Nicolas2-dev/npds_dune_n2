<?php

namespace Npds\View\Contracts;


interface ViewFinderInterface
{

    /**
     * Obtenez l’emplacement complet de la vue.
     *
     * @param string $view Nom de la vue
     * @return string Chemin complet vers la vue
     */
    public function find(string $view): string;

    /**
     * Ajoutez une extension de vue valide au Finder.
     *
     * @param string $extension Extension à ajouter (ex: 'tpl', 'php')
     * @return void
     */
    public function addExtension(string $extension): void;

    /**
     * Ajoutez un emplacement au chercheur.
     *
     * @param string $location Chemin à ajouter
     * @return void
     */
    public function addLocation(string $location): void;

    /**
     * Ajoutez un indice d'espace de noms au chercheur.
     *
     * @param string          $namespace Nom de l'espace de noms
     * @param string|string[] $hints     Chemins associés à l'espace de noms
     * @return void
     */
    public function addNamespace(string $namespace, string|array $hints): void;

    /**
     * Ajoutez un chemin spécifié par son espace de noms pour les overrides.
     *
     * @param string $namespace Nom de l'espace de noms
     * @return void
     */
    public function overridesFrom(string $namespace): void;

}
