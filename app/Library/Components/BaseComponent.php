<?php

namespace App\Library\Components;

use App\Library\Components\Contracts\ComponentInterface;

/**
 * Composant de base abstrait.
 * Fournit les fonctionnalités communes à tous les composants,
 * comme la gestion des propriétés et l'échappement de contenu.
 */
abstract class BaseComponent implements ComponentInterface
{
    /**
     * Propriétés du composant.
     * @var array<string, mixed>
     */
    protected array $props = [];

    /**
     * Constructeur du composant.
     *
     * @param array<string, mixed> $props Propriétés initiales du composant.
     */
    public function __construct(array $props = [])
    {
        $this->props = $props;
    }

    /**
     * Récupère une propriété du composant avec une valeur par défaut.
     *
     * @param string $key Clé de la propriété.
     * @param mixed $default Valeur par défaut si la propriété n'existe pas.
     * @return mixed Valeur de la propriété ou valeur par défaut.
     */
    protected function prop(string $key, mixed $default = null): mixed
    {
        return $this->props[$key] ?? $default;
    }

    /**
     * Échappe une chaîne pour l'affichage HTML.
     *
     * @param string $value Chaîne à échapper.
     * @return string Chaîne échappée (HTML entities).
     */
    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
