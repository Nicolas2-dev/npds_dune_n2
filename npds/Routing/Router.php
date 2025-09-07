<?php

namespace Npds\Routing;

use Closure;
use LogicException;
use Npds\Http\Request;
use Npds\Exceptions\Http\NotFoundHttpException;

class Router
{

    /**
     * Instance unique du routeur (singleton).
     *
     * @var Router|null
     */
    protected static ?Router $instance = null;

    /**
     * Liste des routes par méthode HTTP.
     *
     * @var array<string, array<string, array>>
     */
    protected array $routes = [
        'GET'     => [],
        'POST'    => [],
        'PUT'     => [],
        'DELETE'  => [],
        'PATCH'   => [],
        'HEAD'    => [],
        'OPTIONS' => [],
    ];

    /**
     * Patterns globaux pour les paramètres des routes.
     *
     * @var array<string, string>
     */
    protected array $patterns = [];

    /**
     * Récupère l’instance unique du routeur.
     *
     * @return Router
     */
    public static function getInstance(): Router
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * Définit une route qui répond à toutes les méthodes HTTP principales.
     *
     * @param string $route  Le chemin de la route.
     * @param mixed  $action L’action à exécuter (closure ou "Controller@method").
     *
     * @return mixed
     */
    public function any(string $route, mixed $action): mixed
    {
        $methods = array('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD');

        return $this->match($methods, $route, $action);
    }

    /**
     * Définit une route pour des méthodes HTTP spécifiques.
     *
     * @param array<int, string> $methods  Méthodes HTTP autorisées.
     * @param string             $route    Chemin de la route.
     * @param mixed              $action   Action à exécuter.
     *
     * @return void
     */
    public function match(array $methods, string $route, mixed $action): void
    {
        $methods = array_map('strtoupper', $methods);

        if (in_array('GET', $methods) && ! in_array('HEAD', $methods)) {
            $methods[] = 'HEAD';
        }

        $route = '/' .trim($route, '/');

        if (! is_array($action)) {
            $action = array('uses' => $action);
        }

        foreach ($methods as $method) {
            if (array_key_exists($method, $this->routes)) {
                $this->routes[$method][$route] = $action;
            }
        }
    }

    /**
     * Traite la requête et retourne la réponse correspondant à une route.
     *
     * @param Request $request Requête HTTP à dispatcher.
     *
     * @return mixed
     *
     * @throws NotFoundHttpException Si aucune route ne correspond.
     */
    public function dispatch(Request $request): mixed
    {
        $method = $request->method();

        $path = $request->path();

        // Récupère les routes par méthode HTTP.
        $routes = isset($this->routes[$method]) ? $this->routes[$method] : array();

        foreach ($routes as $route => $action) {
            $wheres = isset($action['where']) ? $action['where'] : array();

            $pattern = $this->compileRoute($route, array_merge($this->patterns, $wheres));

            if (preg_match($pattern, $path, $matches) !== 1) {
                continue;
            }

            $parameters = array_filter($matches, function ($value, $key)
            {
                return is_string($key) && ! empty($value);

            }, ARRAY_FILTER_USE_BOTH);

            $callback = isset($action['uses']) ? $action['uses'] : $this->findActionClosure($action);

            return $this->call($callback, $parameters);
        }

        throw new NotFoundHttpException('Page not found');
    }

    /**
     * Compile une route en expression régulière.
     *
     * @param string            $route    Route à compiler.
     * @param array<string, string> $patterns Patterns personnalisés pour les paramètres.
     *
     * @return string Expression régulière prête pour preg_match.
     */
    protected function compileRoute(string $route, array $patterns): string
    {
        $optionals = 0;

        $variables = array();

        $regexp = preg_replace_callback('#/\{(.*?)(\?)?\}#', function ($matches) use ($route, $patterns, &$optionals, &$variables)
        {
            @list(, $name, $optional) = $matches;

            if (in_array($name, $variables)) {
                throw new LogicException("Pattern [$route] cannot reference variable name [$name] more than once.");
            }

            $variables[] = $name;

            $pattern = isset($patterns[$name]) ? $patterns[$name] : '[^/]+';

            if ($optional) {
                $optionals++;

                return sprintf('(?:/(?P<%s>%s)', $name, $pattern);
            } else if ($optionals > 0) {
                throw new LogicException("Pattern [$route] cannot reference variable [$name] after one or more optionals.");
            }

            return sprintf('/(?P<%s>%s)', $name, $pattern);

        }, $route);

        if ($optionals > 0) {
            $regexp .= str_repeat(')?', $optionals);
        }

        return '#^' .$regexp .'$#s';
    }

    /**
     * Recherche une closure dans la définition d’une route.
     *
     * @param array<string, mixed> $action Action définie pour la route.
     *
     * @return Closure|null
     */
    protected function findActionClosure(array $action): ?Closure
    {
        foreach ($action as $key => $value) {
            if (is_numeric($key) && ($value instanceof Closure)) {
                return $value;
            }
        }

        // Aucun Closure trouvé
        return null;
    }

    /**
     * Appelle l’action correspondante à une route.
     *
     * @param mixed        $callback   Closure ou "Controller@method".
     * @param array<string, mixed> $parameters Paramètres à passer.
     *
     * @return mixed
     */
    protected function call(mixed $callback, array $parameters): mixed
    {
        if ($callback instanceof Closure) {
            return call_user_func_array($callback, $parameters);
        }

        list ($controller, $method) = explode('@', $callback);

        if (! class_exists($controller)) {
            throw new LogicException("Controller [$controller] not found.");
        }

        // Crée l’instance du contrôleur et vérifie la méthode spécifiée.
        else if (! method_exists($instance = new $controller(), $method)) {
            throw new LogicException("Controller [$controller] has no method [$method].");
        }

        return $instance->callAction($method, $parameters);
    }

    /**
     * Définit un pattern pour un paramètre de route.
     *
     * @param string $key     Nom du paramètre.
     * @param string $pattern Expression régulière.
     *
     * @return void
     */
    public function pattern(string $key, string $pattern): void
    {
        $this->patterns[$key] = $pattern;
    }

    /**
     * Appel dynamique pour ajouter des routes via méthodes HTTP (get, post, put...).
     *
     * @param string $method     Méthode appelée.
     * @param array  $parameters Paramètres de la méthode.
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (array_key_exists($key = strtoupper($method), $this->routes)) {
            array_unshift($parameters, array($key));

            $method = 'match';
        }

        return call_user_func_array(array($this, $method), $parameters);
    }

}
