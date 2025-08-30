<?php

namespace Npds\Routing;

use Npds\Http\Request;
use Npds\Collections\Arr;
use Npds\Routing\Matching\UriValidator;
use Npds\Routing\Matching\HostValidator;
use Npds\Routing\Matching\MethodValidator;
use Npds\Routing\Matching\SchemeValidator;
use Symfony\Component\Routing\Route as SymfonyRoute;


class Route
{

    /**
     * The URI pattern the route responds to.
     *
     * @var string
     */
    public $uri;

    /**
     * The HTTP methods the route responds to.
     *
     * @var array
     */
    public $methods;

    /**
     * The route action array.
     *
     * @var array
     */
    public $action;






    /**
     * The regular expression requirements.
     *
     * @var array
     */
    public $wheres = [];











    /**
     * The compiled version of the route.
     *
     * @var \Symfony\Component\Routing\CompiledRoute
     */
    public $compiled;






    /**
     * The fields that implicit binding should use for a given parameter.
     *
     * @var array
     */
    protected $bindingFields = [];

    /**
     * The validators used by the routes.
     *
     * @var array
     */
    public static $validators;


    /**
     * Create a new Route instance.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array  $action
     * @return void
     */
    public function __construct($methods, $uri, $action)
    {
        $this->uri = $uri;
        $this->methods = (array) $methods;
        $this->action = Arr::except($this->parseAction($action), ['prefix']);

        if (in_array('GET', $this->methods) && ! in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }

        $this->prefix(is_array($action) ? Arr::get($action, 'prefix') : '');
    }


    /**
     * Parse the route action into a standard array.
     *
     * @param  callable|array|null  $action
     * @return array
     *
     * @throws \UnexpectedValueException
     */
    protected function parseAction($action)
    {
        return RouteAction::parse($this->uri, $action);
    }










    /**
     * Determine if the route matches a given request.
     *
     * @param  \Npds\Http\Request  $request
     * @param  bool  $includingMethod
     * @return bool
     */
    public function matches(Request $request, $includingMethod = true)
    {
        $this->compileRoute();

        foreach (self::getValidators() as $validator) {
            if (! $includingMethod && $validator instanceof MethodValidator) {
                continue;
            }

            if (! $validator->matches($this, $request)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Compile the route into a Symfony CompiledRoute instance.
     *
     * @return \Symfony\Component\Routing\CompiledRoute
     */
    protected function compileRoute()
    {
        if (! $this->compiled) {
            $this->compiled = $this->toSymfonyRoute()->compile();
        }

        return $this->compiled;
    }











    /**
     * Get the domain defined for the route.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return isset($this->action['domain'])
                ? str_replace(['http://', 'https://'], '', $this->action['domain']) : null;
    }







    /**
     * Add a prefix to the route URI.
     *
     * @param  string  $prefix
     * @return $this
     */
    public function prefix($prefix)
    {
        $prefix ??= '';

        $this->updatePrefixOnAction($prefix);

        $uri = rtrim($prefix, '/').'/'.ltrim($this->uri, '/');

        return $this->setUri($uri !== '/' ? trim($uri, '/') : $uri);
    }

    /**
     * Update the "prefix" attribute on the action array.
     *
     * @param  string  $prefix
     * @return void
     */
    protected function updatePrefixOnAction($prefix)
    {
        if (! empty($newPrefix = trim(rtrim($prefix, '/').'/'.ltrim($this->action['prefix'] ?? '', '/'), '/'))) {
            $this->action['prefix'] = $newPrefix;
        }
    }

    /**
     * Get the URI associated with the route.
     *
     * @return string
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Set the URI that the route responds to.
     *
     * @param  string  $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $this->parseUri($uri);

        return $this;
    }

    /**
     * Parse the route URI and normalize / store any implicit binding fields.
     *
     * @param  string  $uri
     * @return string
     */
    protected function parseUri($uri)
    {
        $this->bindingFields = [];

        return tap(RouteUri::parse($uri), function ($uri) {
            $this->bindingFields = $uri->bindingFields;
        })->uri;
    }

















    /**
     * Get the route validators for the instance.
     *
     * @return array
     */
    public static function getValidators()
    {
        if (isset(static::$validators)) {
            return static::$validators;
        }

        // To match the route, we will use a chain of responsibility pattern with the
        // validator implementations. We will spin through each one making sure it
        // passes and then we will know if the route as a whole matches request.
        return static::$validators = [
            new UriValidator, new MethodValidator,
            new SchemeValidator, new HostValidator,
        ];
    }   

    /**
     * Convert the route to a Symfony route.
     *
     * @return \Symfony\Component\Routing\Route
     */
    public function toSymfonyRoute()
    {
        return new SymfonyRoute(
            preg_replace('/\{(\w+?)\?\}/', '{$1}', $this->uri()), $this->getOptionalParameterNames(),
            $this->wheres, ['utf8' => true],
            $this->getDomain() ?: '', [], $this->methods
        );
    }

    /**
     * Get the optional parameter names for the route.
     *
     * @return array
     */
    protected function getOptionalParameterNames()
    {
        preg_match_all('/\{(\w+?)\?\}/', $this->uri(), $matches);

        return isset($matches[1]) ? array_fill_keys($matches[1], null) : [];
    }







}
