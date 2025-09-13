<?php

namespace Npds\Filesystem;

use SplFileInfo;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;


class Filefinder
{

    /** 
     * @var array Chemins où rechercher 
     */
    protected array $paths = [];

    /** 
     * @var bool Ne retourner que les fichiers 
     */
    protected bool $onlyFiles = false;

    /** 
     * @var bool Ne retourner que les dossiers 
     */
    protected bool $onlyDirs = false;

    /** 
     * @var int Profondeur maximale de recherche (-1 = illimitée) 
     */
    protected int $maxDepth = -1;

    /** 
     * @var array Modèles de noms de fichiers à inclure 
     */
    protected array $namePatterns = [];

    /** 
     * @var array Modèles de noms de fichiers à exclure 
     */
    protected array $notNamePatterns = [];

    /**
     * @var array Extensions de fichiers à filtrer 
     */
    protected array $extensions = [];

    /** 
     * @var string|null Contenu devant être présent dans les fichiers 
     */
    protected ?string $contains = null;

    /** 
     * @var bool|null Filtrer les fichiers vides ou non 
     */
    protected ?bool $isEmpty = null;

    /** 
     * @var bool Ignorer les fichiers commençant par un point 
     */
    protected bool $ignoreDotFiles = false;

    /** 
     * @var array Dossiers à exclure 
     */
    protected array $excludeDirs = [];

    /** 
     * @var callable|null Fonction de filtrage personnalisée */
    protected $filter = null;

    /** 
     * @var bool Trier les résultats par nom 
     */
    protected bool $sortByName = false;

    /** 
     * @var bool Trier les résultats par date de modification 
     */
    protected bool $sortByModified = false;

    /**
     * Crée une nouvelle instance de SimpleFinder
     *
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * Définir les chemins de recherche
     *
     * @param string|array $paths
     * @return static
     */
    public function in(string|array $paths): static
    {
        foreach ((array)$paths as $path) {
            $this->paths[] = rtrim($path, DIRECTORY_SEPARATOR);
        }

        return $this;
    }

    /**
     * Ne rechercher que les fichiers
     *
     * @return static
     */
    public function files(): static
    {
        $this->onlyFiles = true;
        $this->onlyDirs = false;

        return $this;
    }

    /**
     * Ne rechercher que les dossiers
     *
     * @return static
     */
    public function directories(): static
    {
        $this->onlyDirs = true;
        $this->onlyFiles = false;

        return $this;
    }

    /**
     * Définir la profondeur maximale de recherche
     *
     * @param int $depth
     * @return static
     */
    public function depth(int $depth): static
    {
        $this->maxDepth = $depth;

        return $this;
    }

    /**
     * Ajouter des motifs de noms à inclure
     *
     * @param string|array $patterns
     * @return static
     */
    public function name(string|array $patterns): static
    {
        $this->namePatterns = array_merge($this->namePatterns, (array)$patterns);

        return $this;
    }

    /**
     * Ajouter des motifs de noms à exclure
     *
     * @param string|array $patterns
     * @return static
     */
    public function notName(string|array $patterns): static
    {
        $this->notNamePatterns = array_merge($this->notNamePatterns, (array)$patterns);

        return $this;
    }

    /**
     * Filtrer par extensions de fichiers
     *
     * @param array $exts
     * @return static
     */
    public function extensions(array $exts): static
    {
        $this->extensions = $exts;

        return $this;
    }

    /**
     * Rechercher uniquement les fichiers contenant ce texte
     *
     * @param string $text
     * @return static
     */
    public function contains(string $text): static
    {
        $this->contains = $text;

        return $this;
    }

    /**
     * Filtrer les fichiers vides ou non
     *
     * @param bool $isEmpty
     * @return static
     */
    public function empty(bool $isEmpty = true): static
    {
        $this->isEmpty = $isEmpty;

        return $this;
    }

    /**
     * Ignorer les fichiers commençant par un point
     *
     * @param bool $ignore
     * @return static
     */
    public function ignoreDotFiles(bool $ignore = true): static
    {
        $this->ignoreDotFiles = $ignore;

        return $this;
    }

    /**
     * Exclure certains dossiers
     *
     * @param array|string $dirs
     * @return static
     */
    public function exclude(array|string $dirs): static
    {
        $this->excludeDirs = array_merge($this->excludeDirs, (array)$dirs);

        return $this;
    }

    /**
     * Appliquer un filtre personnalisé
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $this->filter = $callback;

        return $this;
    }

    /**
     * Trier par nom
     *
     * @param bool $sort
     * @return static
     */
    public function sortByName(bool $sort = true): static
    {
        $this->sortByName = $sort;

        return $this;
    }

    /**
     * Trier par date de modification
     *
     * @param bool $sort
     * @return static
     */
    public function sortByModified(bool $sort = true): static
    {
        $this->sortByModified = $sort;

        return $this;
    }

    /**
     * Retourne le tableau de fichiers ou dossiers trouvés
     *
     * @return SplFileInfo[]
     */
    public function get(): array
    {
        $results = [];

        foreach ($this->paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                $depth = $iterator->getDepth();

                if ($this->maxDepth >= 0 && $depth > $this->maxDepth) {
                    continue;
                }

                if ($this->onlyFiles && !$item->isFile()) {
                    continue;
                }

                if ($this->onlyDirs && !$item->isDir()) {
                    continue;
                }

                if ($this->ignoreDotFiles && str_starts_with($item->getFilename(), '.')) {
                    continue;
                }

                if ($this->isExcluded($item)) {
                    continue;
                }

                if (!$this->matchName($item->getFilename())) {
                    continue;
                }

                if (!$this->matchNotName($item->getFilename())) {
                    continue;
                }

                if (!$this->matchExtensions($item)) {
                    continue;
                }

                if (!$this->matchContains($item)) {
                    continue;
                }

                if (!$this->matchEmpty($item)) {
                    continue;
                }

                if (!$this->matchFilter($item)) {
                    continue;
                }

                $results[] = $item;
            }
        }

        if ($this->sortByName) {
            usort($results, fn($a, $b) => strcasecmp($a->getFilename(), $b->getFilename()));
        } elseif ($this->sortByModified) {
            usort($results, fn($a, $b) => filemtime($a->getPathname()) <=> filemtime($b->getPathname()));
        }

        return $results;
    }

    /**
     * Vérifie si le nom du fichier correspond aux motifs définis.
     *
     * @param string $filename Nom du fichier à tester
     * @return bool Retourne true si le fichier correspond à au moins un motif, sinon false
     */
    protected function matchName(string $filename): bool
    {
        if (empty($this->namePatterns)) {
            return true;
        }

        foreach ($this->namePatterns as $pattern) {
            if (fnmatch($pattern, $filename)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si le nom du fichier ne correspond pas aux motifs exclus.
     *
     * @param string $filename Nom du fichier à tester
     * @return bool Retourne true si le fichier ne correspond à aucun motif exclu, sinon false
     */
    protected function matchNotName(string $filename): bool
    {
        if (empty($this->notNamePatterns)) {
            return true;
        }

        foreach ($this->notNamePatterns as $pattern) {
            if (fnmatch($pattern, $filename)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Vérifie si le fichier a l'une des extensions autorisées.
     *
     * @param SplFileInfo $file Fichier à tester
     * @return bool Retourne true si le fichier a une extension autorisée ou si aucun filtre n'est défini
     */
    protected function matchExtensions(SplFileInfo $file): bool
    {
        if (empty($this->extensions)) {
            return true;
        }

        if (!$file->isFile()) {
            return true;
        }

        foreach ($this->extensions as $ext) {
            if (str_ends_with($file->getFilename(), '.' . ltrim($ext, '.'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si le contenu du fichier contient une chaîne spécifique.
     *
     * @param SplFileInfo $file Fichier à tester
     * @return bool Retourne true si le contenu contient la chaîne ou si aucun filtre n'est défini
     */
    protected function matchContains(SplFileInfo $file): bool
    {
        if ($this->contains === null || !$file->isFile()) {
            return true;
        }

        $content = file_get_contents($file->getPathname());

        return str_contains($content, $this->contains);
    }

    /**
     * Vérifie si le fichier est vide ou non, selon le filtre défini.
     *
     * @param SplFileInfo $file Fichier à tester
     * @return bool Retourne true si le fichier correspond au filtre vide/non vide
     */
    protected function matchEmpty(SplFileInfo $file): bool
    {
        if ($this->isEmpty === null || !$file->isFile()) {
            return true;
        }

        return $this->isEmpty ? filesize($file->getPathname()) === 0 : filesize($file->getPathname()) > 0;
    }

    /**
     * Vérifie si le fichier ou dossier est dans la liste des dossiers exclus.
     *
     * @param SplFileInfo $item Fichier ou dossier à tester
     * @return bool Retourne true si le fichier/dossier doit être exclu, sinon false
     */
    protected function isExcluded(SplFileInfo $item): bool
    {
        if (empty($this->excludeDirs)) {
            return false;
        }

        foreach ($this->excludeDirs as $dir) {
            if (str_starts_with($item->getPathname(), $dir)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Applique un filtre personnalisé défini par l'utilisateur.
     *
     * @param SplFileInfo $item Fichier ou dossier à tester
     * @return bool Retourne true si le fichier/dossier passe le filtre, sinon false
     */
    protected function matchFilter(SplFileInfo $item): bool
    {
        if (!$this->filter) {
            return true;
        }

        return (bool) call_user_func($this->filter, $item);
    }
}
