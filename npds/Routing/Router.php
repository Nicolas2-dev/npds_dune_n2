<?php

namespace Npds\Routing;

use Npds\Container\Container;
use Npds\Contracts\Events\Dispatcher;
use Npds\Contracts\Routing\BindingRegistrar;
use Npds\Contracts\Routing\Registrar as RegistrarContract;



/**
 * @mixin \Npds\Routing\RouteRegistrar
 */
class Router implements BindingRegistrar, RegistrarContract
{



    /**
     * The event dispatcher instance.
     *
     * @var \Npds\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The IoC container instance.
     *
     * @var \Npds\Container\Container
     */
    protected $container;

    /**
     * The route collection instance.
     *
     * @var \Npds\Routing\RouteCollectionInterface
     */
    protected $routes;



    /**
     * The priority-sorted list of middleware.
     *
     * Forces the listed middleware to always be in the given order.
     *
     * @var array
     */
    public $middlewarePriority = [];











    /**
     * All of the verbs supported by the router.
     *
     * @var string[]
     */
    public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    
    /**
     * Create a new Router instance.
     *
     * @param  \Npds\Contracts\Events\Dispatcher  $events
     * @param  \Npds\Container\Container|null  $container
     * @return void
     */
    public function __construct(Dispatcher $events, ?Container $container = null)
    {
        $this->events = $events;
        $this->routes = new RouteCollection;
        $this->container = $container ?: new Container;
    }









}
