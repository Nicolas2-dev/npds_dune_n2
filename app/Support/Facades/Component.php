<?php

namespace App\Support\Facades;

use App\Library\Components\ComponentManager;


class Component
{

    /**
     * Appelle dynamiquement un composant via son nom comme méthode statique.
     *
     * Exemple : Component::footer($args)
     *
     * @param string $method     Nom du composant appelé.
     * @param array  $parameters Paramètres passés au composant.
     *
     * @return string Le rendu HTML du composant.
     */
    public static function __callStatic(string $method, array $parameters): string
    {
        $manager = ComponentManager::getInstance();

        return $manager->renderComponent($method, ...$parameters);
    }

    /**
     * Rend un composant explicitement par son nom.
     *
     * @param string $componentName Nom du composant.
     * @param mixed ...$args        Arguments passés au composant.
     *
     * @return string Le rendu HTML du composant.
     */
    public static function render(string $componentName, ...$args): string
    {
        $manager = ComponentManager::getInstance();
        
        return $manager->renderComponent($componentName, $args);
    }

    /**
     * Définit le chemin vers le dossier des composants.
     *
     * @param string $path Chemin vers les composants.
     *
     * @return void
     */
    public static function setPath(string $path): void
    {
        ComponentManager::getInstance($path);
    }

    /**
     * Retourne la liste de tous les composants chargés.
     *
     * @return array Liste des noms de composants.
     */
    public static function all(): array
    {
        return ComponentManager::getInstance()->getLoadedComponents();
    }

    /**
     * Vérifie si un composant existe.
     *
     * @param string $componentName Nom du composant.
     *
     * @return bool True si le composant existe, false sinon.
     */
    public static function exists(string $componentName): bool
    {
        return ComponentManager::getInstance()->hasComponent($componentName);
    }

}
