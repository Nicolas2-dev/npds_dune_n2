<?php

namespace Npds\Foundation\Http;


use Throwable;
use Npds\Contracts\Foundation\Application;
use Npds\Contracts\Http\Kernel as KernelContract;
use Npds\Routing\Pipeline;
use Npds\Routing\Router;
use Npds\Support\Carbon;


class Kernel implements KernelContract
{


    /**
     * The application implementation.
     *
     * @var \Npds\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The router instance.
     *
     * @var \Npds\Routing\Router
     */
    protected $router;

    /**
     * The bootstrap classes for the application.
     *
     * @var string[]
     */
    protected $bootstrappers = [
        //\Npds\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        //\Npds\Foundation\Bootstrap\LoadConfiguration::class,
        //\Npds\Foundation\Bootstrap\HandleExceptions::class,
        //\Npds\Foundation\Bootstrap\RegisterFacades::class,
        //\Npds\Foundation\Bootstrap\RegisterProviders::class,
        //\Npds\Foundation\Bootstrap\BootProviders::class,
    ];

    /**
     * The application's middleware stack.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [];

    /**
     * All of the registered request duration handlers.
     *
     * @var array
     */
    protected $requestLifecycleDurationHandlers = [];

    /**
     * When the kernel starting handling the current request.
     *
     * @var \Npds\Support\Carbon|null
     */
    protected $requestStartedAt;

    /**
     * The priority-sorted list of middleware.
     *
     * Forces non-global middleware to always be in the given order.
     *
     * @var string[]
     */
    protected $middlewarePriority = [
        // \Npds\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        // \Npds\Cookie\Middleware\EncryptCookies::class,
        // \Npds\Session\Middleware\StartSession::class,
        // \Npds\View\Middleware\ShareErrorsFromSession::class,
        // \Npds\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        // \Npds\Routing\Middleware\ThrottleRequests::class,
        // \Npds\Routing\Middleware\ThrottleRequestsWithRedis::class,
        // \Npds\Contracts\Session\Middleware\AuthenticatesSessions::class,
        // \Npds\Routing\Middleware\SubstituteBindings::class,
        // \Npds\Auth\Middleware\Authorize::class,
    ];

    /**
     * Create a new HTTP kernel instance.
     *
     * @param  \Npds\Contracts\Foundation\Application  $app
     * @param  \Npds\Routing\Router  $router
     * @return void
     */
    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;

        $this->router = $router;

        $this->syncMiddlewareToRouter();
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param  \Npds\Http\Request  $request
     * @return \Npds\Http\Response
     */
    public function handle($request)
    {
        $this->requestStartedAt = Carbon::now();

        //try {
            $request->enableHttpMethodParameterOverride();

            $response = $this->sendRequestThroughRouter($request);
        //} catch (Throwable $e) {
        //    $this->reportException($e);
        
        //    $response = $this->renderException($request, $e);
        //}

        //$this->app['events']->dispatch(
        //    new RequestHandled($request, $response)
        //);

        return $response;
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param  \Npds\Http\Request  $request
     * @return \Npds\Http\Response
     */
    protected function sendRequestThroughRouter($request)
    {
        $this->app->instance('request', $request);

        //Facade::clearResolvedInstance('request');

        $this->bootstrap();

        return (new Pipeline($this->app))
                    ->send($request)
                    ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
                    ->then($this->dispatchToRouter());
    }

    /**
     * Bootstrap the application for HTTP requests.
     *
     * @return void
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter()
    {
        return function ($request) {
            $this->app->instance('request', $request);

            return $this->router->dispatch($request);
        };
    }













    /**
     * Sync the current state of the middleware to the router.
     *
     * @return void
     */
    protected function syncMiddlewareToRouter()
    {
        $this->router->middlewarePriority = $this->middlewarePriority;

        foreach ($this->middlewareGroups as $key => $middleware) {
            $this->router->middlewareGroup($key, $middleware);
        }

        foreach ($this->routeMiddleware as $key => $middleware) {
            $this->router->aliasMiddleware($key, $middleware);
        }
    }

    

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }



}
