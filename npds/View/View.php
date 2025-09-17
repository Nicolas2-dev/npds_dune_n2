<?php

namespace Npds\View;

use Closure;
use Exception;
use ArrayAccess;
use Npds\Support\Arr;
use Npds\Support\Str;
use Npds\View\Factory;
use BadMethodCallException;
use Npds\View\Contracts\EngineInterface;
use Npds\Support\Contracts\ArrayableInterface as Arrayable;
use Npds\Support\Contracts\RenderableInterface as Renderable;


class View implements ArrayAccess, Renderable
{

    /**
     * Factory des vues.
     *
     * @var Factory
     */
    protected Factory $factory;

    /**
     * Moteur de rendu de la vue (PhpEngine ou CompilerEngine ou FileEngine).
     *
     * @var EngineInterface
     */
    protected EngineInterface $engine;

    /**
     * Nom de la vue.
     *
     * @var string|null
     */
    protected ?string $view = null;

    /**
     * Chemin complet du fichier de la vue.
     *
     * @var string|null
     */
    protected ?string $path = null;

    /**
     * Données disponibles pour la vue.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Indique si la vue utilise un layout.
     *
     * @var bool
     */
    protected bool $layout = false;


    /**
     * Constructeur.
     *
     * @param Factory          $factory Factory de vues.
     * @param EngineInterface  $engine  Moteur de rendu.
     * @param string           $view    Nom de la vue.
     * @param string           $path    Chemin complet du fichier de la vue.
     * @param array|Arrayable  $data    Données à passer à la vue.
     */
    public function __construct(Factory $factory, EngineInterface $engine, string $view, string $path, array|Arrayable $data = [])
    {
        $this->factory = $factory;
        $this->engine  = $engine;

        //
        $this->view = $view;
        $this->path = $path;

        $this->data = ($data instanceof Arrayable) ? $data->toArray() : (array) $data;
    }

    /**
     * Rend la vue.
     *
     * @param Closure|null $callback Fonction callback à exécuter après rendu.
     * @return string Contenu rendu.
     * @throws Exception
     */
    public function render(?Closure $callback = null): string
    {
        try {
            $contents = $this->renderContents();

            $response = isset($callback) ? $callback($this, $contents) : null;

            // Une fois que nous avons le contenu de la vue, nous viderons les sections si nous sommes
            // terminé le rendu de toutes les vues afin qu'il ne reste plus rien en suspens lorsque
            // une autre vue sera rendue ultérieurement par le développeur de l'application.
            $this->factory->flushSectionsIfDoneRendering();

            return $response ?: $contents;
        }
        catch (Exception $e) {
            $this->factory->flushSections();

            throw $e;
        }
    }

    /**
     * Récupère le contenu de la vue.
     *
     * @return string Contenu rendu de la vue.
     */
    public function renderContents(): string
    {
        // Incrémente le compteur de vues rendues pour gérer les sections.
        $this->factory->incrementRender();

        // Appelle les composeurs éventuels.
        $this->factory->callComposer($this);

        $contents = $this->getContents();

        // Décrémente le compteur après rendu pour vider les sections correctement.
        $this->factory->decrementRender();

        return $contents;
    }

    /**
     * Rend toutes les sections de la vue.
     *
     * Retourne le contenu des sections collectées pendant le rendu des vues.
     *
     * @return array Contenu des sections rendues
     */
    public function renderSections()
    {
        return $this->render(function ($view)
        {
            return $this->factory->getSections();
        });
    }

    /**
     * Récupère le contenu en utilisant le moteur de rendu.
     *
     * @return string Contenu rendu.
     */
    protected function getContents(): string
    {
        return $this->engine->get($this->path, $this->gatherData());
    }

    /**
     * Fusionne les données partagées et locales pour le rendu.
     *
     * @return array Données finales pour la vue.
     */
    public function gatherData(): array
    {
        $data = array_merge($this->factory->getShared(), $this->data);

        return array_map(function ($value)
        {
            return ($value instanceof Renderable) ? $value->render() : $value;

        }, $data);
    }

    /**
     * Crée une vue imbriquée.
     *
     * <code>
     *     // Add a View instance to a View's data
     *     $view = View::make('foo')->nest('footer', 'Partials/Footer');
     *
     *     // Equivalent functionality using the "with" method
     *     $view = View::make('foo')->with('footer', View::make('Partials/Footer'));
     * </code>
     * 
     * @param string $key   Clé pour stocker la sous-vue.
     * @param string $view  Nom de la vue imbriquée.
     * @param array  $data  Données pour la sous-vue.
     * @return $this
     */
    public function nest(string $key, string $view, array $data = []): static
    {
        // L'instance de View imbriquée hérite des données parent si aucune n'est donnée.
        if (empty($data)) {
            $data = $this->getData();
        }

        return $this->with($key, $this->factory->make($view, $data));
    }

    /**
     * Associe une donnée à la vue.
     *
     * @param string|array $key Clé ou tableau de données.
     * @param mixed        $value Valeur à associer.
     * @return $this
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
     * Ajoute des erreurs à la vue.
     *
     * @param MessageProvider|array $provider Source des messages d'erreurs.
     * @return $this
     */
    public function withErrors(MessageProvider|array $provider): static
    {
        if ($provider instanceof MessageProvider) {
            $this->with('errors', $provider->getMessageBag());
        } else {
            $this->with('errors', new MessageBag((array) $provider));
        }
    
        return $this;
    }

    /**
     * Partage une donnée globalement via la factory.
     *
     * @param string $key   Clé de la donnée.
     * @param mixed  $value Valeur à partager.
     * @return $this
     */
    public function shares(string $key, mixed $value): static
    {
        $this->factory->share($key, $value);

        return $this;
    }

    /**
     * Définit ou récupère le layout utilisé.
     *
     * @param bool|null $value True pour activer, false pour désactiver, null pour récupérer.
     * @return bool|static
     */
    public function layout(?bool $value = null): bool|static
    {
        if (is_null($value)) {
            return $this->layout;
        }

        $this->layout = (bool) $value;

        return $this;
    }

    /**
     * Récupère la factory associée.
     *
     * @return Factory
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }

    /**
     * Récupère le nom de la vue.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->view;
    }

    /**
     * Récupère les données associées à la vue.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Récupère le chemin complet du fichier de la vue.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Définit le chemin complet du fichier de la vue.
     *
     * @param string $path Chemin complet.
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Vérifie si une clé existe dans les données (ArrayAccess).
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * Récupère une valeur via une clé (ArrayAccess).
     */
    public function offsetGet(mixed $offset): mixed
    {
        return Arr::get($this->data, $offset);
    }

    /**
     * Définit une valeur pour une clé (ArrayAccess).
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * Supprime une clé des données (ArrayAccess).
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * Accède à une donnée via propriété magique.
     */
    public function __get(string $key): mixed
    {
        return Arr::get($this->data, $key);
    }

    /**
     * Définit une donnée via propriété magique.
     */
    public function __set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Vérifie si une donnée existe via propriété magique.
     */
    public function __isset(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Permet les appels de méthodes dynamiques, ex: withX().
     */
    public function __call(string $method, array $params): mixed
    {
        // Ajoutez la prise en charge des méthodes dynamiques withX.
        if (Str::startsWith($method, 'with')) {
            $name = Str::camel(substr($method, 4));

            return $this->with($name, array_shift($params));
        }

        throw new BadMethodCallException("ERROR : La méthode [$method] n'existe pas dans " . get_class($this));
    }

    /**
     * Rend la vue lorsque l'objet est utilisé comme chaîne.
     */
    public function __toString(): string
    {
        try {
            return $this->render();
        } catch (Exception $e) {
            //return $e; //'';
            return '';
        }
    }

}
