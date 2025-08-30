<?php

namespace Npds\Routing;

use Npds\Http\Request;
use Npds\Collections\Arr;



class RouteCollection extends AbstractRouteCollection
{

    /**
     * An array of the routes keyed by method.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * A flattened array of all of the routes.
     *
     * @var \Npds\Routing\Route[]
     */
    protected $allRoutes = [];



















    /**
     * Find the first route matching a given request.
     *
     * @param  \Npds\Http\Request  $request
     * @return \Npds\Routing\Route
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function match(Request $request)
    {
        $routes = $this->get($request->getMethod());

        // First, we will see if we can find a matching route for this current request
        // method. If we can, great, we can just return it so that it can be called
        // by the consumer. Otherwise we will check for routes with another verb.
        $route = $this->matchAgainstRoutes($routes, $request);

        return $this->handleMatchedRoute($request, $route);
    }

    /**
     * Get routes from the collection by method.
     *
     * @param  string|null  $method
     * @return \Npds\Routing\Route[]
     */
    public function get($method = null)
    {
        return is_null($method) ? $this->getRoutes() : Arr::get($this->routes, $method, []);
    }










    /**
     * Get all of the routes in the collection.
     *
     * @return \Npds\Routing\Route[]
     */
    public function getRoutes()
    {
        return array_values($this->allRoutes);
    }








}
