<?php

namespace Npds\View;

use Closure;
use Npds\View\Views;
use RuntimeException;
use BadMethodCallException;
use Npds\Events\Dispatcher;
use InvalidArgumentException;
use Npds\View\Engines\EngineResolver;
use Npds\Support\Contracts\ArrayableInterface as Arrayable;


class Factory
{

    /**
     * Résolveur des moteurs de rendu.
     *
     * @var EngineResolver
     */
    protected EngineResolver $engines;

    /**
     * Chercheur de fichiers de vues.
     *
     * @var FileViewFinder
     */
    protected FileViewFinder $finder;

    /**
     * Gestionnaire d'événements.
     *
     * @var Dispatcher
     */
    protected Dispatcher $events;

    /**
     * Données partagées entre toutes les vues.
     *
     * @var array
     */
    protected array $shared = [];

    /**
     * Alias de vues.
     *
     * @var array
     */
    protected array $aliases = [];

    /**
     * Extensions de fichiers et moteurs associés.
     *
     * @var array
     */
    protected array $extensions = [
        'tpl' => 'template',
        'php' => 'php',
        'css' => 'file',
        'js'  => 'file',
    ];

    /**
     * Sections de vue enregistrées.
     *
     * @var array
     */
    protected array $sections = [];

    /**
     * Pile des sections en cours de rendu.
     *
     * @var array
     */
    protected array $sectionStack = [];

    /**
     * Compteur de rendu des vues en cours.
     *
     * @var int
     */
    protected int $renderCount = 0;

    /**
     * Instance unique du singleton.
     *
     * @var self|null
     */
    protected static ?self $instance = null;


    /**
     * Constructeur privé pour empêcher l'instanciation directe.
     */
    private function __construct(EngineResolver $engines, FileViewFinder $finder, Dispatcher $events)
    {
        $this->engines = $engines;
        $this->finder  = $finder;
        $this->events  = $events;

        $this->share('__env', $this);
    }

    /**
     * Crée ou retourne l’instance unique.
     */
    public static function getInstance(?EngineResolver $engines = null, ?FileViewFinder $finder = null, ?Dispatcher $events = null): self
    {
        if (static::$instance === null) {
            if ($engines === null || $finder === null || $events === null) {
                throw new RuntimeException("Erreur : le singleton Factory n’a pas été initialisé. Fournissez EngineResolver, FileViewFinder et Dispatcher lors du premier appel.");
            }

            static::$instance = new self($engines, $finder, $events);
        }

        return static::$instance;
    }

    /**
     * Crée une instance de vue.
     *
     * @param string $view Nom de la vue
     * @param array|Arrayable $data Données à passer à la vue
     * @param array $mergeData Données supplémentaires à fusionner
     * @return Views
     */
    public function make(string $view, array|Arrayable $data = [], array $mergeData = []): Views
    {
        if (isset($this->aliases[$view])) $view = $this->aliases[$view];

        $path = $this->finder->find($view);

        if (is_null($path) || ! is_readable($path)) {
            throw new BadMethodCallException("Erreur : le fichier [$path] est introuvable");
        }

        $data = array_except(
            array_merge($mergeData, $this->parseData($data)), array('__data', '__path')
        );

        $this->callCreator(
            $view = new Views($this, $this->getEngineFromPath($path), $view, $path, $data)
        );

        return $view;
    }

    /**
     * Rend une vue et retourne le contenu.
     *
     * @param string $view
     * @param array|Arrayable $data
     * @param Closure|null $callback
     * @return string
     */
    public function fetch(string $view, array|Arrayable $data = [], ?Closure $callback = null): string
    {
        unset($data['__path'], $data['__path']);

        return $this->make($view, $data)->render($callback);
    }

    /**
     * Convertit les données en tableau si elles implémentent Arrayable.
     *
     * @param array|Arrayable $data
     * @return array
     */
    protected function parseData(array|Arrayable $data): array
    {
        return ($data instanceof Arrayable) ? $data->toArray() : $data;
    }

    /**
     * Vérifie si une vue existe.
     *
     * @param string $view
     * @return bool
     */
    public function exists(string $view): bool
    {
        try {
            $this->finder->find($view);
        }
        catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * Récupère le moteur adapté à l'extension du fichier.
     *
     * @param string $path
     * @return mixed
     */
    public function getEngineFromPath(string $path): mixed
    {
        $extension = $this->getExtension($path);

        $engine = $this->extensions[$extension];

        return $this->engines->resolve($engine);
    }

    /**
     * Récupère l'extension du fichier pour déterminer le moteur.
     *
     * @param string $path
     * @return string|null
     */
    protected function getExtension(string $path): ?string
    {
        $extensions = array_keys($this->extensions);

        return array_first($extensions, function($key, $value) use ($path)
        {
            return ends_with($path, $value);
        });
    }

    /**
     * Partage une donnée avec toutes les vues.
     *
     * @param string|array $key
     * @param mixed $value
     */
    public function share(string|array $key, mixed $value = null): void
    {
        if (!is_array($key)) {
            $this->shared[$key] = $value;
            return;
        }

        foreach ($key as $innerKey => $innerValue) {
            $this->share($innerKey, $innerValue);
        }
    }

    /**
     * Appelle les listeners "composing" pour une vue.
     *
     * @param Views $view
     */
    public function callComposer(Views $view): void
    {
        $this->events->fire('composing: ' .$view->getName(), array($view));
    }

    /**
     * Appelle les listeners "creating" pour une vue.
     *
     * @param Views $view
     */
    public function callCreator(Views $view): void
    {
        $this->events->fire('creating: ' .$view->getName(), array($view));
    }

    /**
     * Réinitialise toutes les sections de rendu.
     */
    public function flushSections(): void
    {
        $this->renderCount = 0;

        $this->sections = array();

        $this->sectionStack = array();
    }

    /**
     * Réinitialise les sections si toutes les vues ont été rendues.
     */
    public function flushSectionsIfDoneRendering(): void
    {
        if ($this->doneRendering()) {
            $this->flushSections();
        }
    }

    /**
     * Incrémente le compteur de rendu.
     */
    public function incrementRender(): void
    {
        $this->renderCount++;
    }

    /**
     * Décrémente le compteur de rendu.
     */
    public function decrementRender(): void
    {
        $this->renderCount--;
    }

    /**
     * Vérifie si toutes les vues ont été rendues.
     *
     * @return bool
     */
    public function doneRendering(): bool
    {
        return ($this->renderCount == 0);
    }

    /**
     * Récupère les données partagées.
     *
     * @return array
     */
    public function getShared(): array
    {
        return $this->shared;
    }



    /**
     * Configurez les chemins pour le remplacement des vues.
     *
     * @param  string  $namespace
     * @return void
     */
    public function overridesFrom($namespace)
    {
        $this->finder->overridesFrom($namespace);
    }

    /**
     * Ajoutez un nouvel espace de noms au chargeur.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function addNamespace($namespace, $hints)
    {
        $this->finder->addNamespace($namespace, $hints);
    }

}
