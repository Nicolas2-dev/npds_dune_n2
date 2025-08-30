<?php

namespace Npds\Container;

use Closure;
use Exception;
use TypeError;
use ArrayAccess;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Npds\Contracts\Container\BindingResolutionException;
use Npds\Contracts\Container\CircularDependencyException;
use Npds\Contracts\Container\Container as ContainerContract;


class Container implements ArrayAccess, ContainerContract
{

    /**
     * Le conteneur global actuellement disponible (s’il existe).
     *
     * @var static
     */
    protected static $instance;

    /**
     * Un tableau des types déjà résolus.
     *
     * @var bool[]
     */
    protected $resolved = [];

    /**
     * Les liaisons enregistrées dans le conteneur.
     *
     * @var array[]
     */
    protected $bindings = [];

    /**
     * Les liaisons de méthodes du conteneur.
     *
     * @var \Closure[]
     */
    protected $methodBindings = [];

    /**
     * Les instances partagées (singleton) du conteneur.
     *
     * @var object[]
     */
    protected $instances = [];

    /**
     * Les instances à portée (scoped) du conteneur.
     *
     * @var array
     */
    protected $scopedInstances = [];

    /**
     * Les alias de types enregistrés.
     *
     * @var string[]
     */
    protected $aliases = [];

    /**
     * Les alias enregistrés, indexés par nom abstrait.
     *
     * @var array[]
     */
    protected $abstractAliases = [];

    /**
     * Les closures d’extension pour les services.
     *
     * @var array[]
     */
    protected $extenders = [];

    /**
     * Tous les tags enregistrés.
     *
     * @var array[]
     */
    protected $tags = [];

    /**
     * La pile des concrétions en cours de construction.
     *
     * @var array[]
     */
    protected $buildStack = [];

    /**
     * La pile de surcharge des paramètres.
     *
     * @var array[]
     */
    protected $with = [];

    /**
     * La carte des liaisons contextuelles.
     *
     * @var array[]
     */
    public $contextual = [];

    /**
     * Tous les callbacks enregistrés pour les rebonds (rebound).
     *
     * @var array[]
     */
    protected $reboundCallbacks = [];

    /**
     * Tous les callbacks globaux avant la résolution d’un service.
     *
     * @var \Closure[]
     */
    protected $globalBeforeResolvingCallbacks = [];

    /**
     * Tous les callbacks globaux lors de la résolution d’un service.
     *
     * @var \Closure[]
     */
    protected $globalResolvingCallbacks = [];

    /**
     * Tous les callbacks globaux après la résolution d’un service.
     *
     * @var \Closure[]
     */
    protected $globalAfterResolvingCallbacks = [];

    /**
     * Tous les callbacks avant résolution, spécifiques à un type de classe.
     *
     * @var array[]
     */
    protected $beforeResolvingCallbacks = [];

    /**
     * Tous les callbacks lors de la résolution, spécifiques à un type de classe.
     *
     * @var array[]
     */
    protected $resolvingCallbacks = [];

    /**
     * Tous les callbacks après résolution, spécifiques à un type de classe.
     *
     * @var array[]
     */
    protected $afterResolvingCallbacks = [];


    /**
     * Détermine si le type abstrait donné a déjà été résolu.
     *
     * @param  string  $abstract  Nom du type abstrait
     * @return bool  Retourne vrai si le type a été résolu, faux sinon
     */
    public function resolved($abstract)
    {
        // Si le type donné est un alias, on récupère le vrai nom du type
        if ($this->isAlias($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        // On vérifie si le type est marqué comme résolu ou s’il existe déjà une instance partagée
        return isset($this->resolved[$abstract]) ||
            isset($this->instances[$abstract]);
    }


    /**
     * Détermine si un type donné est partagé (singleton).
     *
     * @param  string  $abstract  Nom du type abstrait
     * @return bool  Retourne vrai si le type est partagé, faux sinon
     */
    public function isShared($abstract)
    {
        // Si une instance existe déjà pour ce type, il est forcément partagé
        // OU si le type est défini dans les bindings avec 'shared' à true
        return isset($this->instances[$abstract]) ||
            (isset($this->bindings[$abstract]['shared']) &&
                $this->bindings[$abstract]['shared'] === true);
    }



    /**
     * Détermine si une chaîne donnée est un alias.
     *
     * @param  string  $name  Le nom à vérifier
     * @return bool  Retourne vrai si c’est un alias, faux sinon
     */
    public function isAlias($name)
    {
        // Vérifie si le nom existe dans le tableau des alias enregistrés
        return isset($this->aliases[$name]);
    }


    /**
     * Enregistrer une liaison dans le conteneur.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     *
     * @throws \TypeError
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        // Supprime les instances existantes obsolètes pour ce type
        $this->dropStaleInstances($abstract);

        // Si aucun type concret n'a été donné, nous allons simplement définir le type concret
        // sur le type abstrait. Après cela, le type concret sera enregistré comme partagé
        // sans être obligé de spécifier leurs classes dans les deux paramètres.
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        // Si l’usine (factory) n’est pas une Closure, cela signifie que c’est simplement
        // un nom de classe qui est lié dans ce conteneur au type abstrait et nous allons
        // simplement l’encapsuler dans sa propre Closure pour nous offrir plus de commodité
        // lors de l’extension.
        if (! $concrete instanceof Closure) {
            if (! is_string($concrete)) {
                throw new TypeError(self::class.'::bind(): L’argument #2 ($concrete) doit être de type Closure|string|null');
            }

            // Transforme le nom de classe en Closure pour pouvoir l’exécuter plus tard
            $concrete = $this->getClosure($abstract, $concrete);
        }

        // Enregistre la liaison dans le conteneur avec le concret et le flag 'shared'
        $this->bindings[$abstract] = compact('concrete', 'shared');

        // Si le type abstrait a déjà été résolu dans ce conteneur, nous allons déclencher
        // le listener de réenregistrement afin que tous les objets déjà résolus
        // puissent avoir leur copie de l’objet mise à jour via les callbacks du listener.
        if ($this->resolved($abstract)) {
            $this->rebound($abstract);
        }
    }

    /**
     * Obtenir la Closure à utiliser lors de la construction d’un type.
     *
     * @param  string  $abstract  Le type abstrait
     * @param  string  $concrete  Le type concret (la classe à instancier)
     * @return \Closure  Retourne une Closure qui construira l’objet
     */
    protected function getClosure($abstract, $concrete)
    {
        // Retourne une Closure qui recevra le conteneur et d’éventuels paramètres
        return function ($container, $parameters = []) use ($abstract, $concrete) {

            // Si le type abstrait est le même que le type concret,
            // on construit directement la classe avec build()
            if ($abstract == $concrete) {
                return $container->build($concrete);
            }

            // Sinon, on résout le type concret via le conteneur.
            // $raiseEvents = false signifie qu’on ne déclenche pas les callbacks globaux/resolving
            return $container->resolve(
                $concrete, $parameters, $raiseEvents = false
            );
        };
    }

    /**
     * Determine if the container has a method binding.
     *
     * @param  string  $method
     * @return bool
     */
    public function hasMethodBinding($method)
    {
        return isset($this->methodBindings[$method]);
    }






    /**
     * Get the method binding for the given method.
     *
     * @param  string  $method
     * @param  mixed  $instance
     * @return mixed
     */
    public function callMethodBinding($method, $instance)
    {
        return call_user_func($this->methodBindings[$method], $instance, $this);
    }






    /**
     * Ajouter une liaison contextuelle au conteneur.
     *
     * @param  string  $concrete
     * @param  string  $abstract
     * @param  \Closure|string  $implementation
     * @return void
     */
    public function addContextualBinding($concrete, $abstract, $implementation)
    {
        $this->contextual[$concrete][$this->getAlias($abstract)] = $implementation;
    }




    /**
     * Enregistrer une liaison partagée dans le conteneur.
     *
     * @param  string  $abstract  Le type abstrait à lier
     * @param  \Closure|string|null  $concrete  La classe concrète ou une Closure pour l’instance
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        // Appelle la méthode bind() avec le troisième paramètre à true
        // Cela signifie que le conteneur va créer **une seule instance** et la partager
        $this->bind($abstract, $concrete, true);
    }







    /**
     * Register an existing instance as shared in the container.
     *
     * @param  string  $abstract
     * @param  mixed  $instance
     * @return mixed
     */
    public function instance($abstract, $instance)
    {
        $this->removeAbstractAlias($abstract);

        $isBound = $this->bound($abstract);

        unset($this->aliases[$abstract]);

        // We'll check to determine if this type has been bound before, and if it has
        // we will fire the rebound callbacks registered with the container and it
        // can be updated with consuming classes that have gotten resolved here.
        $this->instances[$abstract] = $instance;

        if ($isBound) {
            $this->rebound($abstract);
        }

        return $instance;
    }

    /**
     * Remove an alias from the contextual binding alias cache.
     *
     * @param  string  $searched
     * @return void
     */
    protected function removeAbstractAlias($searched)
    {
        if (! isset($this->aliases[$searched])) {
            return;
        }

        foreach ($this->abstractAliases as $abstract => $aliases) {
            foreach ($aliases as $index => $alias) {
                if ($alias == $searched) {
                    unset($this->abstractAliases[$abstract][$index]);
                }
            }
        }
    }







    /**
     * Alias a type to a different name.
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     *
     * @throws \LogicException
     */
    public function alias($abstract, $alias)
    {
        if ($alias === $abstract) {
            throw new LogicException("[{$abstract}] is aliased to itself.");
        }

        $this->aliases[$alias] = $abstract;

        $this->abstractAliases[$abstract][] = $alias;
    }




    


    /**
     * Déclencher les callbacks de “re-liaison” pour le type abstrait donné.
     *
     * @param  string  $abstract  Le type abstrait dont il faut relancer les callbacks
     * @return void
     */
    protected function rebound($abstract)
    {
        // On crée ou récupère l’instance du type demandé
        $instance = $this->make($abstract);

        // On parcourt tous les callbacks enregistrés pour ce type et on les exécute
        foreach ($this->getReboundCallbacks($abstract) as $callback) {
            $callback($this, $instance);
        }
    }


    /**
     * Récupérer les callbacks de re-liaison pour un type donné.
     *
     * @param  string  $abstract
     * @return array
     */
    protected function getReboundCallbacks($abstract)
    {
        // Retourne les callbacks enregistrés pour ce type, ou un tableau vide si aucun
        return $this->reboundCallbacks[$abstract] ?? [];
    }





    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array<string, mixed>  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function call($callback, array $parameters = [], $defaultMethod = null)
    {
        $pushedToBuildStack = false;

        if (is_array($callback) && ! in_array(
            $className = (is_string($callback[0]) ? $callback[0] : get_class($callback[0])),
            $this->buildStack,
            true
        )) {
            $this->buildStack[] = $className;

            $pushedToBuildStack = true;
        }

        $result = BoundMethod::call($this, $callback, $parameters, $defaultMethod);

        if ($pushedToBuildStack) {
            array_pop($this->buildStack);
        }

        return $result;
    }





    /**
     * Résoudre le type donné à partir du conteneur.
     *
     * @param  string|callable  $abstract  Le type abstrait ou une fonction anonyme à résoudre
     * @param  array  $parameters           Les paramètres supplémentaires pour la résolution
     * @return mixed                        L’instance résolue
     *
     * @throws \Npds\Contracts\Container\BindingResolutionException
     */
    public function make($abstract, array $parameters = [])
    {
        // Délègue le travail réel à la méthode resolve()
        return $this->resolve($abstract, $parameters);
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function get(string $id)
    {
        try {
            return $this->resolve($id);
        } catch (Exception $e) {
            if ($this->has($id) || $e instanceof CircularDependencyException) {
                throw $e;
            }

            throw new EntryNotFoundException($id, is_int($e->getCode()) ? $e->getCode() : 0, $e);
        }
    }




    /**
     * Déterminer si le type abstrait donné a été lié.
     *
     * @param  string  $abstract  Le nom du type abstrait
     * @return bool               True si le type est enregistré dans le conteneur
     */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]) ||  // Vérifie si un binding existe
            isset($this->instances[$abstract]) || // Vérifie si une instance partagée existe
            $this->isAlias($abstract);            // Vérifie si c’est un alias enregistré
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->bound($id);
    }





    /**
     * Résoudre le type donné à partir du conteneur.
     *
     * @param  string|callable  $abstract
     * @param  array  $parameters
     * @param  bool  $raiseEvents
     * @return mixed
     *
     * @throws \Npds\Contracts\Container\BindingResolutionException
     * @throws \Npds\Contracts\Container\CircularDependencyException
     */
    protected function resolve($abstract, $parameters = [], $raiseEvents = true)
    {
        // Résout l'alias réel du type demandé, au cas où un alias aurait été défini
        $abstract = $this->getAlias($abstract);

        // Tout d'abord, nous allons déclencher les gestionnaires d'événements qui s'occupent
        // de la résolution "avant" des types spécifiques. Cela permet à certains hooks
        // d'ajouter des appels d'extension afin de modifier la manière dont les objets
        // sont résolus pour les types qui les intéressent.
        if ($raiseEvents) {
            $this->fireBeforeResolvingCallbacks($abstract, $parameters);
        }

        // Vérifie s'il existe un type concret contextuel spécifique à cet abstract
        $concrete = $this->getContextualConcrete($abstract);

        // Détermine si nous devons construire l'objet avec des paramètres spécifiques
        $needsContextualBuild = ! empty($parameters) || ! is_null($concrete);

        // Si une instance de ce type est actuellement gérée comme singleton, 
        // nous retournerons simplement l’instance existante au lieu d’en créer une nouvelle, 
        // afin que le développeur puisse continuer à utiliser la même instance de l’objet à chaque fois.
        if (isset($this->instances[$abstract]) && ! $needsContextualBuild) {
            return $this->instances[$abstract];
        }

        // Empile les paramètres spécifiques pour ce build afin qu'ils soient accessibles
        $this->with[] = $parameters;

        // Si aucun type concret n'a été défini, récupère le type concret enregistré
        if (is_null($concrete)) {
            $concrete = $this->getConcrete($abstract);
        }

        // Nous sommes prêts à instancier une instance du type concret enregistré pour
        // cette liaison. Cela va créer l’objet, ainsi que résoudre toutes ses
        // dépendances "imbriquées" de manière récursive jusqu’à ce que tout soit résolu.
        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }

        // Si nous avons défini des "extenders" pour ce type, nous allons les parcourir
        // et les appliquer à l'objet en cours de construction. Cela permet d'étendre
        // les services, par exemple en modifiant leur configuration ou en décorant l'objet.
        foreach ($this->getExtenders($abstract) as $extender) {
            $object = $extender($object, $this);
        }

        // Si le type demandé est enregistré comme singleton, nous voulons mettre en cache
        // l'instance "en mémoire" afin de pouvoir la retourner plus tard sans créer
        // une nouvelle instance complète de l'objet à chaque requête suivante.
        if ($this->isShared($abstract) && ! $needsContextualBuild) {
            $this->instances[$abstract] = $object;
        }

        // Déclenche les callbacks de "résolution" après que l'objet ait été construit
        if ($raiseEvents) {
            $this->fireResolvingCallbacks($abstract, $object);
        }

        // Avant de retourner l'instance, nous allons également définir le drapeau "resolved" sur true
        // et retirer les éventuels paramètres de substitution pour cette construction.
        // Une fois ces deux étapes effectuées, nous serons prêts à renvoyer l'instance de classe complètement construite.
        $this->resolved[$abstract] = true;

        // Retire les paramètres spécifiques du build en cours de la pile
        array_pop($this->with);

        // Retourne l'objet construit et éventuellement étendu
        return $object;
    }   

    /**
     * Obtenir le type concret pour un abstrait donné.
     *
     * @param  string|callable  $abstract
     * @return mixed
     */
    protected function getConcrete($abstract)
    {
        // Vérifie si un binding (liaison) existe pour l'abstract dans le conteneur
        if (isset($this->bindings[$abstract])) {
            // Si oui, retourne le type concret associé à cette liaison
            return $this->bindings[$abstract]['concrete'];
        }

        // Si aucun binding n'est défini, retourne simplement l'abstract
        // On suppose que l'abstract est lui-même un nom concret que le conteneur peut instancier
        return $abstract;
    }


    /**
     * Obtenir la liaison concrète contextuelle pour l’abstrait donné.
     *
     * @param  string|callable  $abstract
     * @return \Closure|string|array|null
     */
    protected function getContextualConcrete($abstract)
    {
        // On cherche d'abord si un binding contextuel existe pour cet abstract
        if (! is_null($binding = $this->findInContextualBindings($abstract))) {
            return $binding; // Retourne le binding contextuel si trouvé
        }

        // Vérifie si l'abstract a des alias définis
        if (empty($this->abstractAliases[$abstract])) {
            return; // Si pas d'alias, aucune binding contextuel n'existe
        }

        // Parcourt tous les alias de cet abstract pour trouver un binding contextuel
        foreach ($this->abstractAliases[$abstract] as $alias) {
            if (! is_null($binding = $this->findInContextualBindings($alias))) {
                return $binding; // Retourne le binding contextuel trouvé pour un alias
            }
        }

        // Si aucun binding contextuel n'est trouvé, retourne null implicitement
    }


    /**
     * Trouver la liaison concrète pour l’abstrait donné dans le tableau des liaisons contextuelles.
     *
     * @param  string|callable  $abstract
     * @return \Closure|string|null
     */
    protected function findInContextualBindings($abstract)
    {
        // Cherche le binding contextuel pour l'abstract en cours de build.
        // 'end($this->buildStack)' récupère le dernier type en cours de construction.
        // Retourne le binding s'il existe, sinon null.
        return $this->contextual[end($this->buildStack)][$abstract] ?? null;
    }

    /**
     * Déterminer si le concret donné peut être instancié.
     *
     * @param  mixed  $concrete
     * @param  string  $abstract
     * @return bool
     */
    protected function isBuildable($concrete, $abstract)
    {
        // Un concrete est buildable si c'est exactement l'abstract
        // ou si c'est un Closure qui peut être exécuté pour produire l'instance.
        return $concrete === $abstract || $concrete instanceof Closure;
    }


    /**
     * Instancier une instance concrète du type donné.
     *
     * @param  \Closure|string  $concrete
     * @return mixed
     *
     * @throws \Npds\Contracts\Container\BindingResolutionException
     * @throws \Npds\Contracts\Container\CircularDependencyException
     */
    public function build($concrete)
    {
        // Si le concrete est une Closure, on l'exécute directement.
        // On lui passe le container et les éventuels paramètres de substitution.
        if ($concrete instanceof Closure) {
            return $concrete($this, $this->getLastParameterOverride());
        }

        // Crée un ReflectionClass pour analyser la classe à instancier.
        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            // Si la classe n'existe pas, on lance une exception de résolution.
            throw new BindingResolutionException("Target class [$concrete] does not exist.", 0, $e);
        }

        // Vérifie si la classe est instanciable (non abstraite, non interface)
        if (! $reflector->isInstantiable()) {
            return $this->notInstantiable($concrete);
        }

        // Ajoute la classe à la pile de construction pour détecter les dépendances circulaires
        $this->buildStack[] = $concrete;

        // Récupère le constructeur de la classe à l'aide de la réflexion.
        // Si la classe n'a pas de constructeur, $constructor sera null.
        // Ce constructeur servira à identifier les dépendances à injecter.
        $constructor = $reflector->getConstructor();

        // Si la classe n'a pas de constructeur, elle n'a pas de dépendances.
        if (is_null($constructor)) {
            array_pop($this->buildStack); // retire de la pile de construction
            return new $concrete; // instancie directement
        }

        // Récupère les paramètres du constructeur (les dépendances)
        $dependencies = $constructor->getParameters();

        // Résout toutes les dépendances via le container
        try {
            $instances = $this->resolveDependencies($dependencies);
        } catch (BindingResolutionException $e) {
            // Nettoie la pile avant de relancer l'exception
            array_pop($this->buildStack);
            throw $e;
        }

        array_pop($this->buildStack); // Retire la classe de la pile une fois construite

        // Instancie la classe en injectant les dépendances résolues
        return $reflector->newInstanceArgs($instances);
    }


    /**
     * Résout toutes les dépendances à partir des ReflectionParameters.
     *
     * @param  \ReflectionParameter[]  $dependencies  Liste des paramètres du constructeur à résoudre.
     * @return array  Tableau des instances ou valeurs résolues pour chaque dépendance.
     *
     * @throws \Npds\Contracts\Container\BindingResolutionException  Si une dépendance ne peut pas être résolue.
     */
    protected function resolveDependencies(array $dependencies)
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            // Si une valeur de substitution a été définie pour cette construction particulière,
            // on l'utilise à la place. Sinon, on continue la résolution normale et laisse
            // la reflection déterminer automatiquement la dépendance.
            if ($this->hasParameterOverride($dependency)) {
                $results[] = $this->getParameterOverride($dependency);
                continue;
            }

            // Si le type de la dépendance est null, cela signifie que c'est un type primitif
            // (string, int, etc.) ou autre chose qui n'est pas une classe. Dans ce cas,
            // on utilise resolvePrimitive() pour gérer la valeur ou générer une erreur.
            // Sinon, si c'est une classe, on la résout avec resolveClass().
            $result = is_null(Util::getParameterClassName($dependency))
                            ? $this->resolvePrimitive($dependency)
                            : $this->resolveClass($dependency);

            // Si le paramètre est variadique (...$args), on fusionne toutes les valeurs dans le résultat final.
            if ($dependency->isVariadic()) {
                $results = array_merge($results, $result);
            } else {
                $results[] = $result;
            }
        }

        return $results;
    }


    /**
     * Détermine si la dépendance donnée possède une valeur de substitution (override) de paramètre.
     *
     * @param  \ReflectionParameter  $dependency  Le paramètre du constructeur à vérifier.
     * @return bool  True si un override existe, false sinon.
     */
    protected function hasParameterOverride($dependency)
    {
        return array_key_exists(
            $dependency->name, $this->getLastParameterOverride()
        );
    }

    /**
     * Récupère la valeur de substitution pour une dépendance donnée.
     *
     * @param  \ReflectionParameter  $dependency  Le paramètre du constructeur.
     * @return mixed  La valeur de substitution définie.
     */
    protected function getParameterOverride($dependency)
    {
        return $this->getLastParameterOverride()[$dependency->name];
    }

    /**
     * Récupère le dernier ensemble de substitutions de paramètres.
     *
     * @return array  Tableau associatif [nom_paramètre => valeur].
     */
    protected function getLastParameterOverride()
    {
        return count($this->with) ? end($this->with) : [];
    }

    /**
     * Résout une dépendance primitive (non classée, ex : string, int, bool).
     *
     * @param  \ReflectionParameter  $parameter  Le paramètre du constructeur à résoudre.
     * @return mixed  La valeur résolue pour le paramètre.
     *
     * @throws \Npds\Contracts\Container\BindingResolutionException  Si la dépendance ne peut pas être résolue.
     */
    protected function resolvePrimitive(ReflectionParameter $parameter)
    {
        // Vérifie si un binding contextuel existe pour ce paramètre primitif.
        if (! is_null($concrete = $this->getContextualConcrete('$'.$parameter->getName()))) {
            return Util::unwrapIfClosure($concrete, $this);
        }

        // Si une valeur par défaut est disponible pour le paramètre, on l'utilise.
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        // Si le paramètre est variadique (...$args), on retourne un tableau vide.
        if ($parameter->isVariadic()) {
            return [];
        }

        // Si aucune des conditions précédentes n'est remplie, la dépendance est impossible à résoudre.
        $this->unresolvablePrimitive($parameter);
    }


    /**
     * Résout une dépendance basée sur une classe depuis le conteneur.
     *
     * @param  \ReflectionParameter  $parameter  Le paramètre du constructeur à résoudre.
     * @return mixed  L'instance de la classe résolue.
     *
     * @throws \Npds\Contracts\Container\BindingResolutionException  Si la classe ne peut pas être résolue.
     */
    protected function resolveClass(ReflectionParameter $parameter)
    {
        try {
            // Si le paramètre est variadique (ex : ...$services), on utilise la méthode spéciale
            // resolveVariadicClass pour résoudre un tableau d'instances.
            return $parameter->isVariadic()
                        ? $this->resolveVariadicClass($parameter)
                        // Sinon, on récupère le nom de la classe et on demande au conteneur de créer une instance.
                        : $this->make(Util::getParameterClassName($parameter));
        }

        // Si la résolution échoue, on vérifie si le paramètre est optionnel
        // (ex : function foo(Bar $bar = null)) et on retourne sa valeur par défaut.
        catch (BindingResolutionException $e) {
            if ($parameter->isDefaultValueAvailable()) {
                // Supprime la dernière valeur "override" des paramètres fournis pour ce build.
                array_pop($this->with);

                return $parameter->getDefaultValue();
            }

            // Si le paramètre est variadique et non résolu, on retourne un tableau vide.
            if ($parameter->isVariadic()) {
                array_pop($this->with);

                return [];
            }

            // Si aucune solution n'est possible, on relance l'exception.
            throw $e;
        }
    }


    /**
     * Résout une dépendance de type classe variadique depuis le conteneur.
     *
     * @param  \ReflectionParameter  $parameter  Le paramètre variadique à résoudre.
     * @return mixed  Une instance ou un tableau d'instances de la classe.
     */
    protected function resolveVariadicClass(ReflectionParameter $parameter)
    {
        // Récupère le nom de la classe du paramètre.
        $className = Util::getParameterClassName($parameter);

        // Récupère l'alias enregistré pour cette classe dans le conteneur (si défini).
        $abstract = $this->getAlias($className);

        // Vérifie s'il existe une "concrétisation contextuelle" (un tableau de classes concrètes)
        // pour cet alias dans le conteneur.
        if (! is_array($concrete = $this->getContextualConcrete($abstract))) {
            // Si ce n'est pas un tableau, on crée simplement une instance unique de la classe.
            return $this->make($className);
        }

        // Si c'est un tableau, on résout chaque élément individuellement
        // et on renvoie un tableau d'instances.
        return array_map(fn ($abstract) => $this->resolve($abstract), $concrete);
    }


    /**
     * Lance une exception lorsqu'un type concret ne peut pas être instancié.
     *
     * @param  string  $concrete  Le nom de la classe ou du type à instancier.
     * @return void
     *
     * @throws \Npds\Contracts\Container\BindingResolutionException
     */
    protected function notInstantiable($concrete)
    {
        // Si la pile de construction n'est pas vide, on indique dans le message
        // quelles classes étaient en train d'être construites.
        if (! empty($this->buildStack)) {
            $previous = implode(', ', $this->buildStack);

            $message = "Target [$concrete] is not instantiable while building [$previous].";
        } else {
            // Sinon, on indique juste que le type concret n'est pas instanciable.
            $message = "Target [$concrete] is not instantiable.";
        }

        // Lance l'exception spécifique du conteneur
        throw new BindingResolutionException($message);
    }


    /**
     * Lance une exception lorsqu'une dépendance primitive ne peut pas être résolue.
     *
     * @param  \ReflectionParameter  $parameter  Le paramètre réfléchi non résolvable.
     * @return void
     *
     * @throws \Npds\Contracts\Container\BindingResolutionException
     */
    protected function unresolvablePrimitive(ReflectionParameter $parameter)
    {
        // Construire un message d'erreur précis indiquant la dépendance et la classe
        $message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";

        // Lancer l'exception spécifique du conteneur
        throw new BindingResolutionException($message);
    }




    /**
     * Supprime toutes les instances et alias obsolètes pour un type abstrait donné.
     *
     * @param string $abstract Le nom de l'abstrait (interface ou classe)
     * @return void
     */
    protected function dropStaleInstances($abstract)
    {
        // Supprime l'instance existante du conteneur
        unset($this->instances[$abstract]);

        // Supprime également tout alias enregistré pour cet abstrait
        unset($this->aliases[$abstract]);
    }






    /**
     * Exécute tous les callbacks "avant résolution" pour un type abstrait donné.
     *
     * @param string $abstract    Le nom de l'abstrait (interface ou classe) que l'on va résoudre
     * @param array  $parameters  Les paramètres passés lors de la résolution
     * @return void
     */
    protected function fireBeforeResolvingCallbacks($abstract, $parameters = [])
    {
        // Exécute d'abord tous les callbacks globaux définis pour "avant résolution"
        $this->fireBeforeCallbackArray($abstract, $parameters, $this->globalBeforeResolvingCallbacks);

        // Ensuite, exécute les callbacks spécifiques à ce type ou à ses parents
        foreach ($this->beforeResolvingCallbacks as $type => $callbacks) {
            // On déclenche le callback si le type correspond exactement ou si $abstract est un sous-classe/interface de $type
            if ($type === $abstract || is_subclass_of($abstract, $type)) {
                $this->fireBeforeCallbackArray($abstract, $parameters, $callbacks);
            }
        }
    }


    /**
     * Exécute un tableau de callbacks avant la résolution d’un type.
     *
     * Chaque callback reçoit le nom du type abstrait, les paramètres de résolution
     * et le conteneur lui-même.
     *
     * @param string $abstract  Le nom de la classe ou interface à résoudre
     * @param array  $parameters  Les paramètres à injecter lors de la résolution
     * @param array  $callbacks  Tableau de closures à exécuter
     * @return void
     */
    protected function fireBeforeCallbackArray(string $abstract, array $parameters, array $callbacks): void
    {
        foreach ($callbacks as $callback) {
            $callback($abstract, $parameters, $this);
        }
    }


    /**
     * Exécute tous les callbacks après la résolution d’un type.
     *
     * Cette méthode déclenche :
     * 1. Les callbacks globaux de résolution.
     * 2. Les callbacks spécifiques au type.
     * 3. Les callbacks "after resolving".
     *
     * @param string $abstract  Le nom de la classe ou interface résolue
     * @param mixed  $object    L’instance résolue
     * @return void
     */
    protected function fireResolvingCallbacks(string $abstract, mixed $object): void
    {
        // Exécute tous les callbacks globaux appliqués à toutes les résolutions
        $this->fireCallbackArray($object, $this->globalResolvingCallbacks);

        // Récupère les callbacks spécifiques au type et les exécute
        $this->fireCallbackArray(
            $object,
            $this->getCallbacksForType($abstract, $object, $this->resolvingCallbacks)
        );

        // Exécute les callbacks "after resolving" liés à ce type
        $this->fireAfterResolvingCallbacks($abstract, $object);
    }


    /**
     * Exécute tous les callbacks après la résolution d’un type.
     *
     * Cette méthode déclenche :
     * 1. Les callbacks globaux "after resolving".
     * 2. Les callbacks spécifiques au type "after resolving".
     *
     * @param string $abstract  Le nom de la classe ou interface résolue
     * @param mixed  $object    L’instance résolue
     * @return void
     */
    protected function fireAfterResolvingCallbacks(string $abstract, mixed $object): void
    {
        // Exécute tous les callbacks globaux après résolution appliqués à toutes les résolutions
        $this->fireCallbackArray($object, $this->globalAfterResolvingCallbacks);

        // Récupère les callbacks "after resolving" spécifiques au type et les exécute
        $this->fireCallbackArray(
            $object,
            $this->getCallbacksForType($abstract, $object, $this->afterResolvingCallbacks)
        );
    }


    /**
     * Récupère tous les callbacks applicables pour un type donné.
     *
     * Cette méthode filtre les callbacks enregistrés pour chaque type
     * et ne retourne que ceux correspondant exactement au type abstrait
     * ou aux classes parentes de l’objet.
     *
     * @param string $abstract           Le nom de la classe ou interface ciblée
     * @param object $object             L’instance à vérifier
     * @param array  $callbacksPerType   Tableau des callbacks classés par type
     * @return array                     Tableau des callbacks applicables
     */
    protected function getCallbacksForType(string $abstract, object $object, array $callbacksPerType): array
    {
        // Initialise le tableau qui contiendra tous les callbacks correspondants
        $results = [];

        // Parcourt chaque type enregistré avec ses callbacks
        foreach ($callbacksPerType as $type => $callbacks) {
            // Vérifie si le type correspond exactement à l’abstrait
            // ou si l’objet est une instance de ce type
            if ($type === $abstract || $object instanceof $type) {
                // Fusionne les callbacks correspondants dans le résultat final
                $results = array_merge($results, $callbacks);
            }
        }

        // Retourne tous les callbacks applicables pour ce type
        return $results;
    }


    /**
     * Exécute un tableau de callbacks en leur passant un objet.
     *
     * Chaque callback est appelé avec l’objet fourni et le conteneur
     * comme arguments, ce qui permet aux callbacks d’interagir avec
     * l’instance ou le conteneur pendant la résolution.
     *
     * @param mixed $object       L’objet à transmettre aux callbacks
     * @param array $callbacks    Tableau des callbacks à exécuter
     * @return void
     */
    protected function fireCallbackArray($object, array $callbacks): void
    {
        // Parcourt chaque callback du tableau
        foreach ($callbacks as $callback) {
            // Appelle le callback en passant l’objet et le conteneur
            $callback($object, $this);
        }
    }

    /**
     * Get the container's bindings.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Récupère l'alias d'un abstrait si disponible.
     *
     * Si un alias est défini pour l'abstrait donné, la méthode
     * retourne l'alias final (résolution récursive si plusieurs alias),
     * sinon elle retourne l'abstrait lui-même.
     *
     * @param string $abstract  L'abstrait dont on veut l'alias
     * @return string           L'alias final ou l'abstrait
     */
    public function getAlias($abstract): string
    {
        // Vérifie si un alias existe pour cet abstrait
        return isset($this->aliases[$abstract])
            // Si oui, résout récursivement l'alias
            ? $this->getAlias($this->aliases[$abstract])
            // Sinon, retourne l'abstrait lui-même
            : $abstract;
    }

    /**
     * Récupère les callbacks d'extension pour un type donné.
     *
     * Ces callbacks permettent de modifier ou d'étendre l'instance
     * créée avant qu'elle ne soit retournée.
     *
     * @param string $abstract  L'abstrait dont on veut les extenders
     * @return array            Tableau des callbacks d'extension
     */
    protected function getExtenders($abstract): array
    {
        // Retourne les extenders pour l'alias de l'abstrait, ou un tableau vide si aucun
        return $this->extenders[$this->getAlias($abstract)] ?? [];
    }





    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     *
     * @param  \Npds\Contracts\Container\Container|null  $container
     * @return \Npds\Contracts\Container\Container|static
     */
    public static function setInstance(?ContainerContract $container = null)
    {
        return static::$instance = $container;
    }

    /**
     * Détermine si un offset donné existe dans le container.
     *
     * @param string $key  La clé à vérifier
     * @return bool        Vrai si l'abstrait est lié dans le container
     */
    public function offsetExists($key): bool
    {
        // Vérifie si la clé est liée dans le container
        return $this->bound($key);
    }

    /**
     * Récupère la valeur d'un offset donné dans le container.
     *
     * @param string $key  La clé à récupérer
     * @return mixed       L'instance ou la valeur associée
     */
    public function offsetGet($key): mixed
    {
        // Crée ou retourne l'instance associée à la clé
        return $this->make($key);
    }

    /**
     * Définit la valeur d'un offset donné dans le container.
     *
     * @param string $key    La clé à définir
     * @param mixed  $value  La valeur ou closure à lier
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        // Si la valeur n'est pas une closure, on la transforme en closure retournant la valeur
        $this->bind($key, $value instanceof Closure ? $value : fn () => $value);
    }

    /**
     * Supprime un offset donné du container.
     *
     * @param string $key  La clé à supprimer
     * @return void
     */
    public function offsetUnset($key): void
    {
        // Supprime la liaison, l'instance et l'état résolu de la clé
        unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key]);
    }

    /**
     * Accède dynamiquement aux services du container.
     *
     * @param string $key  La clé à récupérer
     * @return mixed       L'instance ou valeur associée
     */
    public function __get($key)
    {
        // Redirige vers offsetGet pour supporter l'accès dynamique
        return $this[$key];
    }

    /**
     * Définit dynamiquement un service dans le container.
     *
     * @param string $key    La clé à définir
     * @param mixed  $value  La valeur ou closure à lier
     * @return void
     */
    public function __set($key, $value)
    {
        // Redirige vers offsetSet pour supporter l'accès dynamique
        $this[$key] = $value;
    }


}
