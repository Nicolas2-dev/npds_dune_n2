<?php

namespace App\Library\Components;

use App\Library\Components\Contracts\ComponentInterface;

/**
 * Gestionnaire de composants pour le rendu dynamique.
 * Permet d'enregistrer des composants, de définir des propriétés globales
 * et de parser du contenu contenant des balises de composants.
 */
class ComponentRenderer
{
    
    /**
     * Liste des composants enregistrés.
     * @var array<string, class-string<ComponentInterface>>
     */
    private array $components = [];

    /**
     * Propriétés globales partagées à tous les composants.
     * @var array<string, mixed>
     */
    private array $globalProps = [];


    /**
     * Constructeur.
     * Initialise le gestionnaire de composants.
     */
    public function __construct()
    {
    }

    /**
     * Enregistre un composant.
     *
     * @param string $name Nom du composant.
     * @param string $componentClass Classe PHP implémentant ComponentInterface.
     * 
     * @throws \InvalidArgumentException Si la classe n'existe pas ou n'implémente pas ComponentInterface.
     */
    public function registerComponent(string $name, string $componentClass): void
    {
        if (!class_exists($componentClass)) {
            throw new \InvalidArgumentException("La classe $componentClass n'existe pas");
        }

        if (!is_subclass_of($componentClass, ComponentInterface::class)) {
            throw new \InvalidArgumentException("$componentClass doit implémenter ComponentInterface");
        }

        $this->components[$name] = $componentClass;
    }

    /**
     * Définit une propriété globale disponible pour tous les composants.
     *
     * @param string $key Nom de la propriété.
     * @param mixed $value Valeur de la propriété.
     */
    public function setGlobalProp(string $key, mixed $value): void
    {
        $this->globalProps[$key] = $value;
    }

    /**
     * Rend le contenu en remplaçant les balises de composants par leur rendu.
     *
     * @param string $content Contenu HTML/texte contenant des composants.
     * @param array<string, mixed> $props Propriétés locales pour ce rendu.
     * 
     * @return string Contenu rendu avec les composants.
     */
    public function render(string $content, array $props = []): string
    {
        $allProps = array_merge($this->globalProps, $props);

        // Parser les composants <Component:name prop="value">content</Component:name>
        $content = preg_replace_callback(
            '/<Component:(\w+)([^>]*)>(.*?)<\/Component:\1>/s',
            function ($matches) use ($allProps) {
                return $this->renderComponent($matches[1], $matches[2], $matches[3], $allProps);
            },
            $content
        );

        // Parser les composants auto-fermants <Component:name prop="value" />
        $content = preg_replace_callback(
            '/<Component:(\w+)([^>]*)\s*\/>/s',
            function ($matches) use ($allProps) {
                return $this->renderComponent($matches[1], $matches[2], '', $allProps);
            },
            $content
        );

        return $content;
    }

    /**
     * Rendu d'un composant spécifique.
     *
     * @param string $name Nom du composant.
     * @param string $propsString Chaîne des propriétés du composant.
     * @param string $content Contenu à l'intérieur du composant.
     * @param array<string, mixed> $globalProps Propriétés globales à fusionner.
     * 
     * @return string Résultat du rendu ou message d'erreur si le composant est manquant ou en erreur.
     */
    private function renderComponent(string $name, string $propsString, string $content, array $globalProps): string
    {
        if (!isset($this->components[$name])) {
            return "[Composant '$name' non trouvé]";
        }

        $props = array_merge($globalProps, $this->parseProps($propsString));
        
        if (!empty(trim($content))) {
            $props['content'] = trim($content);
        }

        try {
            $componentClass = $this->components[$name];
            $component = new $componentClass($props);

            return $component->render();
        } catch (\Exception $e) {
            return "[Erreur dans '$name': " . $e->getMessage() . "]";
        }
    }

    /**
     * Analyse une chaîne d'attributs en tableau associatif.
     * Convertit automatiquement les types simples (bool, int, float).
     *
     * @param string $propsString Chaîne d'attributs (ex: prop="value").
     * @return array<string, mixed> Tableau associatif clé => valeur.
     */
    private function parseProps(string $propsString): array
    {
        $props = [];
        
        if (empty(trim($propsString))) {
            return $props;
        }

        // Parser les attributs prop="value" ou prop='value'
        preg_match_all('/(\w+)=(["\'])(.*?)\2/', $propsString, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $key = $match[1];
            $value = $match[3];
            
            // Conversion des types basiques
            if ($value === 'true') {
                $props[$key] = true;

            } elseif ($value === 'false') {
                $props[$key] = false;

            } elseif (is_numeric($value)) {
                $props[$key] = is_float($value + 0) ? (float) $value : (int) $value;

            } else {
                $props[$key] = $value;
            }
        }

        return $props;
    }
}
