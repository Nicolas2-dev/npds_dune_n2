<?php

namespace Npds\View;

use BadMethodCallException;
use Exception;

class View
{

    /**
     * Chemin complet du fichier de vue.
     *
     * @var string|null
     */
    protected ?string $path = null;

    /**
     * Données spécifiques à cette instance de vue.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Données partagées entre toutes les vues.
     *
     * @var array<string, mixed>
     */
    protected static array $shared = [];

    
    /**
     * Constructeur protégé pour créer une instance de vue.
     *
     * @param string $path Chemin du fichier de vue
     * @param array<string, mixed> $data Données associées à la vue
     *
     * @throws \BadMethodCallException Si le fichier n'existe pas ou n'est pas lisible
     */
    protected function __construct(string $path, array $data = [])
    {
        if (! is_readable($path)) {
            throw new BadMethodCallException("File path [$path] does not exist");
        }

        $this->path = $path;

        $this->data = is_array($data) ? $data : array($data);
    }

    /**
     * Résout le chemin complet d'une vue.
     */
    protected static function resolvePath(string $view): string
    {
        // Si la vue commence par "modules/", c'est un module
        if (str_starts_with($view, 'modules/')) {

            $module = explode('/', $view, 3);

            // Construire le chemin correct : MODULE_PATH/{Module}/Views/{view}.php
            return MODULE_PATH . $module[1] . DS . 'Views' . DS . str_replace('/', DS, $module[2]) . '.php';
        }

        // Sinon c'est dans l'application
        return APPPATH . str_replace('/', DS, "Views/$view.php");
    }

    /**
     * Vérifie si une vue existe.
     */
    public static function exists(string $view): bool
    {
        $path = static::resolvePath($view);
        
        return is_readable($path);
    }

    /**
     * Crée une instance de vue.
     */
    public static function make(string $view, array $data = []): static
    {
        $path = static::resolvePath($view);

        return new static($path, $data);
    }

    /**
     * Rend la vue en chaîne de caractères.
     *
     * @return string Contenu HTML généré
     */
    public function render(): string
    {
        $__data = $this->gatherData();

        ob_start();

        // Extraire les variables de rendu.
        foreach ($__data as $__variable => $__value) {
            ${$__variable} = $__value;
        }

        unset($__variable, $__value);

        try {
            include $this->path;
        }
        catch (Exception $e) {
            ob_get_clean();

            throw $e;
        }

        return ltrim(ob_get_clean());
    }

    /**
     * Rassemble les données de la vue en fusionnant données partagées et locales.
     *
     * @return array<string, mixed>
     */
    protected function gatherData(): array
    {
        $data = array_merge(static::$shared, $this->data);

        foreach ($data as $key => $value) {
            if ($value instanceof View) {
                $data[$key] = $value->render();
            }
        }

        return $data;
    }

    /**
     * Inclut une vue imbriquée dans la vue actuelle.
     *
     * @param string $key Nom de la variable pour la vue imbriquée
     * @param string $view Nom de la vue imbriquée
     * @param array<string, mixed> $data Données de la vue imbriquée
     * @return static
     */
    public function nest(string $key, string $view, array $data = []): static
    {
        return $this->with($key, static::make($view, $data));
    }

    /**
     * Partage une variable pour toutes les vues.
     *
     * @param string $key Nom de la variable
     * @param mixed $value Valeur de la variable
     */
    public static function share(string $key, mixed $value = null): void
    {
        static::$shared[$key] = $value;
    }

    /**
     * Partage une variable pour toutes les vues et retourne l'instance actuelle.
     *
     * @param string $key Nom de la variable
     * @param mixed $value Valeur de la variable
     * @return static
     */
    public function shares(string $key, mixed $value): static
    {
        static::share($key, $value);

        return $this;
    }

    /**
     * Ajoute une variable à la vue.
     *
     * @param string|array<string, mixed> $key Nom de la variable ou tableau de variables
     * @param mixed $value Valeur de la variable (si $key est string)
     * @return static
     */
    public function with(string|array $key, mixed $value = null): static
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Conversion de la vue en chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string
    {
        try {
            return $this->render();
        }
        catch (Exception $e) {
            return '';
        }
    }

    /**
     * Gestion des appels aux méthodes dynamiques withX().
     *
     * @param string $method Nom de la méthode
     * @param array<int, mixed> $params Paramètres passés
     * @return static
     *
     * @throws \BadMethodCallException Si la méthode n'existe pas
     */
    public function __call(string $method, array $params): static
    {
        // Ajouter le support pour les méthodes dynamiques withX
        if (substr($method, 0, 4) == 'with') {
            $name = lcfirst(substr($method, 4));

            return $this->with($name, array_shift($params));
        }

        throw new BadMethodCallException("Method [$method] does not exist");
    }

}
