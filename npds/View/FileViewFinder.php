<?php

namespace Npds\View;

use Npds\Support\Str;
use InvalidArgumentException;
use Npds\Filesystem\Filesystem;
use Npds\View\Contracts\ViewFinderInterface;


class FileViewFinder implements ViewFinderInterface
{

    /**
     * Gestionnaire de fichiers.
     *
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * Liste des chemins où chercher les vues.
     *
     * @var array
     */
    protected array $paths;

    /**
     * Cache des chemins complets des vues déjà trouvées.
     *
     * @var array
     */
    protected array $views = [];

    /**
     * Espaces de noms et chemins associés pour les vues.
     *
     * @var array
     */
    protected array $hints = [];

    /**
     * Extensions de fichiers autorisées pour les vues.
     *
     * @var array
     */
    protected array $extensions = ['tpl', 'php', 'css', 'js'];

    /**
     * Délimiteur utilisé pour identifier un espace de noms dans le nom de la vue.
     */
    const HINT_PATH_DELIMITER = '::';


    /**
     * Constructeur.
     *
     * @param Filesystem   $files      Gestionnaire de fichiers.
     * @param array        $paths      Chemins de recherche des vues.
     * @param array|null   $extensions Extensions autorisées pour les vues.
     */
    public function __construct(Filesystem $files, array $paths, ?array $extensions = null)
    {
        $this->files = $files;
        $this->paths = $paths;

        if (isset($extensions)) {
            $this->extensions = $extensions;
        }
    }

    /**
     * Recherche le chemin complet d'une vue.
     *
     * @param string $name Nom de la vue.
     * @return string Chemin complet vers le fichier de la vue.
     */
    public function find(string $name): string
    {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        if ($this->hasHintInformation($name = trim($name))) {
            return $this->views[$name] = $this->findNamedPathView($name);
        }

        return $this->views[$name] = $this->findInPaths($name, $this->paths);
    }
    
    /**
     * Recherche une vue basée sur un espace de noms.
     *
     * @param string $name Nom de la vue avec espace de noms (ex: 'admin::dashboard').
     * @return string Chemin complet vers le fichier de la vue.
     */
    protected function findNamedPathView(string $name): string
    {
        list($namespace, $view) = $this->getNamespaceSegments($name);

        $paths = $this->hints[$namespace];

        if (Str::endsWith($path = head($this->paths), DS .'Overrides')) {
            $path = $path .DS .'Packages' .DS .$namespace;

            if (! in_array($path, $paths) && $this->files->isDirectory($path)) {
                array_unshift($paths, $path);
            }
        }

        return $this->findInPaths($view, $paths);
    }

    /**
     * Sépare le nom d'une vue en espace de noms et nom réel.
     *
     * @param string $name Nom de la vue.
     * @return array Tableau [espaceDeNoms, nomVue]
     *
     * @throws InvalidArgumentException Si le nom est invalide ou l'espace de noms non défini.
     */
    protected function getNamespaceSegments(string $name): array
    {
        $segments = explode(static::HINT_PATH_DELIMITER, $name);

        if (count($segments) != 2) {
            throw new InvalidArgumentException("Erreur : le nom de la vue [$name] est invalide.");
        }

        if ( ! isset($this->hints[$segments[0]])) {
            throw new InvalidArgumentException("Erreur : aucun chemin d’espace de noms défini pour [{$segments[0]}].");
        }

        return $segments;
    }

    /**
     * Recherche une vue dans un ou plusieurs chemins donnés.
     *
     * @param string $name Nom de la vue.
     * @param array  $paths Chemins où chercher.
     * @return string Chemin complet vers la vue.
     *
     * @throws InvalidArgumentException Si la vue n'est pas trouvée.
     */
    protected function findInPaths(string $name, array $paths): string
    {
        foreach ($paths as $path) {
            foreach ($this->getPossibleViewFiles($name) as $fileName) {
                $viewPath = $path .DS .$fileName;

                if ($this->files->exists($viewPath)) {
                    return $viewPath;
                }
            }
        }

        throw new InvalidArgumentException("Erreur : Vue [$name] introuvable.");
    }

    /**
     * Génère toutes les variantes possibles du nom de fichier de la vue avec extensions.
     *
     * @param string $name Nom de la vue.
     * @return array Liste des fichiers possibles.
     */
    protected function getPossibleViewFiles(string $name): array
    {
        return array_map(function($extension) use ($name)
        {
            return str_replace('.', '/', $name) .'.' .$extension;

        }, $this->extensions);
    }

    /**
     * Ajoute un chemin de recherche de vues.
     *
     * @param string $location Chemin à ajouter.
     * @return void
     */
    public function addLocation(string $location): void
    {
        $this->paths[] = $location;
    }

    /**
     * Ajoute un chemin de recherche de vues en début de liste.
     *
     * @param string $location Chemin à ajouter.
     * @return void
     */
    public function prependLocation(string $location): void
    {
        array_unshift($this->paths, $location);
    }

    /**
     * Ajoute un espace de noms et ses chemins associés.
     *
     * @param string          $namespace Nom de l’espace de noms.
     * @param string|string[] $hints     Chemins associés à l’espace de noms.
     * @return void
     */
    public function addNamespace(string $namespace, string|array $hints): void
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($this->hints[$namespace], $hints);
        }

        $this->hints[$namespace] = $hints;
    }

    /**
     * Ajoute des chemins à un espace de noms en début de liste.
     *
     * @param string          $namespace Nom de l’espace de noms.
     * @param string|string[] $hints     Chemins à ajouter.
     * @return void
     */
    public function prependNamespace(string $namespace, string|array $hints): void
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($hints, $this->hints[$namespace]);
        }

        $this->hints[$namespace] = $hints;
    }

    /**
     * Ajoute une extension de vue valide et la place en début de liste.
     *
     * @param string $extension Extension à ajouter.
     * @return void
     */
    public function addExtension(string $extension): void
    {
        if (($index = array_search($extension, $this->extensions)) !== false) {
            unset($this->extensions[$index]);
        }

        array_unshift($this->extensions, $extension);
    }

    /**
     * Vérifie si le nom de la vue contient une information d'espace de noms.
     *
     * @param string $name Nom de la vue.
     * @return bool True si un espace de noms est présent, false sinon.
     */
    public function hasHintInformation(string $name): bool
    {
        return strpos($name, static::HINT_PATH_DELIMITER) > 0;
    }
    
    /**
     * Ajoute le chemin "Overrides" pour un espace de noms donné si celui-ci existe.
     *
     * @param string $namespace Nom de l’espace de noms.
     * @return void
     */
    public function overridesFrom(string $namespace): void
    {
        if (! isset($this->hints[$namespace])) {
            return;
        }

        $paths = $this->hints[$namespace];

        // Le dossier Views Override doit être situé dans le même répertoire que celui de Views.
        // Par exemple : <BASEPATH>/themes/Bootstrap/Views -> <BASEPATH>/themes/Bootstrap/Override
        $path = dirname(head($paths)) .DS .'Overrides';

        if (! in_array($path, $this->paths) && $this->files->isDirectory($path)) {

            // Si un autre chemin de remplacement de vues a déjà été ajouté, nous le supprimerons.
            if (Str::endsWith(head($this->paths), DS .'Overrides')) {
                array_shift($this->paths);
            }

            array_unshift($this->paths, $path);
        }
    }

    /**
     * Obtenir l’instance du système de fichiers utilisé par le ViewFinder.
     *
     * @return Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->files;
    }

    /**
     * Obtenir tous les chemins de recherche de vues.
     *
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Obtenir tous les indices d’espace de noms et leurs chemins.
     *
     * @return array<string, string[]>
     */
    public function getHints(): array
    {
        return $this->hints;
    }

    /**
     * Obtenir la liste des extensions de fichiers valides pour les vues.
     *
     * @return string[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

}
