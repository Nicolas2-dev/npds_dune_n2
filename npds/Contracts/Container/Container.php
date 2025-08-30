<?php

namespace Npds\Contracts\Container;

use Closure;
use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{
    /**
     * Déterminer si le type abstrait donné a été enregistré.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract);

    /**
     * Créer un alias pour un type sous un nom différent.
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     *
     * @throws \LogicException
     */
    //public function alias($abstract, $alias);

    /**
     * Attribuer un ensemble de tags à une liaison donnée.
     *
     * @param  array|string  $abstracts
     * @param  array|mixed  ...$tags
     * @return void
     */
    //public function tag($abstracts, $tags);

    /**
     * Résoudre toutes les liaisons pour un tag donné.
     *
     * @param  string  $tag
     * @return iterable
     */
    //public function tagged($tag);

    /**
     * Enregistrer une liaison (binding) dans le conteneur.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false);

    /**
     * Enregistrer une liaison (binding) si elle n'a pas déjà été enregistrée.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    //public function bindIf($abstract, $concrete = null, $shared = false);

    /**
     * Enregistrer une liaison partagée dans le conteneur.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null);

    /**
     * Enregistrer une liaison partagée si elle n'a pas déjà été enregistrée.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    //public function singletonIf($abstract, $concrete = null);

    /**
     * Enregistrer une liaison à portée (scopée) dans le conteneur.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    //public function scoped($abstract, $concrete = null);

    /**
     * Enregistrer une liaison à portée (scopée) si elle n'a pas déjà été enregistrée.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    //public function scopedIf($abstract, $concrete = null);

    /**
     * Étendre un type abstrait dans le conteneur.
     *
     * @param  string  $abstract
     * @param  \Closure  $closure
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    //public function extend($abstract, Closure $closure);

    /**
     * Enregistrer une instance existante comme partagée dans le conteneur.
     *
     * @param  string  $abstract
     * @param  mixed  $instance
     * @return mixed
     */
    public function instance($abstract, $instance);

    /**
     * Ajouter une liaison contextuelle au conteneur.
     *
     * @param  string  $concrete
     * @param  string  $abstract
     * @param  \Closure|string  $implementation
     * @return void
     */
    public function addContextualBinding($concrete, $abstract, $implementation);

    /**
     * Définir une liaison contextuelle.
     *
     * @param  string|array  $concrete
     * @return \Npds\Contracts\Container\ContextualBindingBuilder
     */
    //public function when($concrete);

    /**
     * Obtenir une closure pour résoudre le type donné depuis le conteneur.
     *
     * @param  string  $abstract
     * @return \Closure
     */
    //public function factory($abstract);

    /**
     * Vider le conteneur de toutes les liaisons et instances résolues.
     *
     * @return void
     */
    //public function flush();

    /**
     * Résoudre le type donné à partir du conteneur.
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \Npds\Contracts\Container\BindingResolutionException
     */
    public function make($abstract, array $parameters = []);

    /**
     * Appeler le Closure ou la méthode class@method donnée et injecter ses dépendances.
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     */
    public function call($callback, array $parameters = [], $defaultMethod = null);

    /**
     * Déterminer si le type abstrait donné a été résolu.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function resolved($abstract);

    /**
     * Enregistrer un nouveau callback avant la résolution.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    //public function beforeResolving($abstract, ?Closure $callback = null);

    /**
     *Enregistrer un nouveau callback de résolution.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    //public function resolving($abstract, ?Closure $callback = null);

    /**
     * Enregistrer un nouveau callback après résolution.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    //public function afterResolving($abstract, ?Closure $callback = null);
}
