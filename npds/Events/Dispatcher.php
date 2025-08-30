<?php

namespace Npds\Events;

use Npds\Container\Container;
use Npds\Contracts\Events\Dispatcher as DispatcherContract;
use Npds\Contracts\Container\Container as ContainerContract;



class Dispatcher implements DispatcherContract
{


    /**
     * The IoC container instance.
     *
     * @var \Npds\Contracts\Container\Container
     */
    protected $container;






    /**
     * The queue resolver instance.
     *
     * @var callable
     */
    protected $queueResolver;


    /**
     * Create a new event dispatcher instance.
     *
     * @param  \Npds\Contracts\Container\Container|null  $container
     * @return void
     */
    public function __construct(?ContainerContract $container = null)
    {
        $this->container = $container ?: new Container;
    }






    
    /**
     * Set the queue resolver implementation.
     *
     * @param  callable  $resolver
     * @return $this
     */
    public function setQueueResolver(callable $resolver)
    {
        $this->queueResolver = $resolver;

        return $this;
    }


}
