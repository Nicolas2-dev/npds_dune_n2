<?php

namespace App\Library\Components\Contracts;

/**
 * Interface de base pour tous les composants.
 * 
 * Tout composant doit implémenter cette interface et fournir
 * une méthode `render` pour générer sa sortie HTML.
 */
interface ComponentInterface
{
    /**
     * Génère le rendu du composant.
     *
     * @param array<string, mixed> $props Propriétés optionnelles à utiliser pour le rendu.
     * @return string HTML ou contenu généré par le composant.
     */
    public function render(array $props = []): string;
}
