<?php

namespace App\Library\Components;

use Exception;
use ReflectionClass;
use App\Library\Components\BaseComponent;


class ComponentManager
{
    
    /**
     * Instance singleton
     *
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * Liste des composants chargés [nom => classe]
     *
     * @var array<string,string>
     */
    private array $components = [];

    /**
     * Chemin vers le dossier contenant les composants
     *
     * @var string
     */
    private string $componentsPath;

    /**
     * Indique si les composants ont été chargés
     *
     * @var bool
     */
    private bool $loaded = false;


    /**
     * Constructeur privé pour singleton
     *
     * @param string $componentsPath Chemin vers les composants
     */
    private function __construct(string $componentsPath = APPPATH . 'Components')
    {
        $this->componentsPath = rtrim($componentsPath, '/');
    }

    /**
     * Récupère l'instance singleton du ComponentManager
     *
     * @param string $componentsPath Chemin vers les composants
     * 
     * @return self
     */
    public static function getInstance(string $componentsPath = APPPATH . 'Components'): self
    {
        if (self::$instance === null) {
            self::$instance = new self($componentsPath);
        }

        return self::$instance;
    }

    /**
     * Charge tous les composants depuis le dossier
     *
     * @return void
     * @throws Exception Si le dossier n'existe pas
     */
    public function loadComponents(): void
    {
        if ($this->loaded) {
            return;
        }

        if (!is_dir($this->componentsPath)) {
            throw new Exception("Le dossier des composants '{$this->componentsPath}' n'existe pas.");
        }

        $files = glob($this->componentsPath . '/*.php');
        
        foreach ($files as $file) {
            $this->loadComponent($file);
        }

        $this->loaded = true;
    }

    /**
     * Charge un composant spécifique
     *
     * @param string $filePath Chemin vers le fichier du composant
     *
     * @return void
     */
    private function loadComponent(string $filePath): void
    {
        $className = $this->getClassNameFromFile($filePath);
        
        if ($className) {

            require_once $filePath;
            
            if (class_exists($className)) {
                $reflection = new ReflectionClass($className);
                
                // Vérifie que la classe hérite de BaseComponent
                if ($reflection->isSubclassOf(BaseComponent::class)) {
                    $componentName = strtolower(str_replace('Component', '', $className));

                    $this->components[$componentName] = $className;
                }
            }
        }
    }

    /**
     * Extrait le nom de la classe depuis un fichier PHP
     *
     * @param string $filePath Chemin vers le fichier
     *
     * @return string|null Nom de la classe ou null
     */
    private function getClassNameFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        
        // Recherche la déclaration de classe
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * 
     *
     * @param   [type]  $componentName  [$componentName description]
     * @param   [type]  $args           [$args description]
     *
     * @return  []                      [return description]
     */
    public function renderComponent($componentName, $args = [])
    {
        $this->loadComponents();
        
        $componentName = strtolower($componentName);
        
        if (isset($this->components[$componentName])) {
            $className = $this->components[$componentName];
            
            $component = new $className();

            return $component->render($args);
        }
        
        throw new Exception("Composant '{$componentName}' non trouvé.");
    }

    /**
     * 
     *
     * @param   [type]  $componentName  [$componentName description]
     *
     * @return  [type]                  [return description]
     */
    public function hasComponent($componentName)
    {
        $this->loadComponents();

        return isset($this->components[strtolower($componentName)]);
    }

    /**
     * 
     *
     * @return  [type]  [return description]
     */
    public function getLoadedComponents()
    {
        $this->loadComponents();

        return array_keys($this->components);
    }

}
