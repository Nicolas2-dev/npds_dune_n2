<?php

namespace Npds\Routing;


use Countable;
use Traversable;
use ArrayIterator;
use IteratorAggregate;
use Npds\Http\Request;


use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


abstract class AbstractRouteCollection implements Countable, IteratorAggregate, RouteCollectionInterface
{


    /**
     * Handle the matched route.
     *
     * @param  \Npds\Http\Request  $request
     * @param  \Npds\Routing\Route|null  $route
     * @return \Npds\Routing\Route
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function handleMatchedRoute(Request $request, $route)
    {
        if (! is_null($route)) {
            return $route->bind($request);
        }

        // If no route was found we will now check if a matching route is specified by
        // another HTTP verb. If it is we will need to throw a MethodNotAllowed and
        // inform the user agent of which HTTP verb it should use for this route.
        $others = $this->checkForAlternateVerbs($request);

        if (count($others) > 0) {
            return $this->getRouteForMethods($request, $others);
        }

        throw new NotFoundHttpException(sprintf(
            'The route %s could not be found.',
            $request->path()
        ));
    }

    /**
     * Determine if any routes match on another HTTP verb.
     *
     * @param  \Npds\Http\Request  $request
     * @return array
     */
    protected function checkForAlternateVerbs($request)
    {
        $methods = array_diff(Router::$verbs, [$request->getMethod()]);

        // Here we will spin through all verbs except for the current request verb and
        // check to see if any routes respond to them. If they do, we will return a
        // proper error response with the correct headers on the response string.
        return array_values(array_filter(
            $methods,
            function ($method) use ($request) {
                return ! is_null($this->matchAgainstRoutes($this->get($method), $request, false));
            }
        ));
    }

    /**
     * Determine if a route in the array matches the request.
     *
     * @param  \Npds\Routing\Route[]  $routes
     * @param  \Npds\Http\Request  $request
     * @param  bool  $includingMethod
     * @return \Npds\Routing\Route|null
     */
    protected function matchAgainstRoutes(array $routes, $request, $includingMethod = true)
    {
        [$fallbacks, $routes] = collect($routes)->partition(function ($route) {
            return $route->isFallback;
        });

        return $routes->merge($fallbacks)->first(
            fn (Route $route) => $route->matches($request, $includingMethod)
        );
    }

    /**
     * Get a route (if necessary) that responds when other available methods are present.
     *
     * @param  \Npds\Http\Request  $request
     * @param  string[]  $methods
     * @return \Npds\Routing\Route
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function getRouteForMethods($request, array $methods)
    {
        if ($request->isMethod('OPTIONS')) {
            return (new Route('OPTIONS', $request->path(), function () use ($methods) {
                return new Response('', 200, ['Allow' => implode(',', $methods)]);
            }))->bind($request);
        }

        $this->requestMethodNotAllowed($request, $methods, $request->method());
    }

    /**
     * Throw a method not allowed HTTP exception.
     *
     * @param  \Npds\Http\Request  $request
     * @param  array  $others
     * @param  string  $method
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function requestMethodNotAllowed($request, array $others, $method)
    {
        throw new MethodNotAllowedHttpException(
            $others,
            sprintf(
                'The %s method is not supported for route %s. Supported methods: %s.',
                $method,
                $request->path(),
                implode(', ', $others)
            )
        );
    }




    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getRoutes());
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->getRoutes());
    }

}
