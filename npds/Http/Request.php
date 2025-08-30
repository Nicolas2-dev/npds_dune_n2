<?php

namespace Npds\Http;

use ArrayAccess;

use Npds\Contracts\Support\Arrayable;

use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;


/**
 * @method array validate(array $rules, ...$params)
 * @method array validateWithBag(string $errorBag, array $rules, ...$params)
 * @method bool hasValidSignature(bool $absolute = true)
 */
class Request extends SymfonyRequest implements Arrayable, ArrayAccess
{


    use Concerns\InteractsWithContentTypes,
        Concerns\InteractsWithInput;

    /**
     * The decoded JSON content for the request.
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag|null
     */
    protected $json;




    /**
     * Create a new Illuminate HTTP request from server variables.
     *
     * @return static
     */
    public static function capture()
    {
        static::enableHttpMethodParameterOverride();

        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }






    /**
     * Get the request method.
     *
     * @return string
     */
    public function method()
    {
        return $this->getMethod();
    }







    /**
     * Get the current path info for the request.
     *
     * @return string
     */
    public function path()
    {
        $pattern = trim($this->getPathInfo(), '/');

        return $pattern === '' ? '/' : $pattern;
    }












    /**
     * Get the JSON payload for the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return \Symfony\Component\HttpFoundation\ParameterBag|mixed
     */
    public function json($key = null, $default = null)
    {
        if (! isset($this->json)) {
            $this->json = new InputBag((array) json_decode($this->getContent(), true));
        }

        if (is_null($key)) {
            return $this->json;
        }

        return data_get($this->json->all(), $key, $default);
    }



    /**
     * Get the input source for the request.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInputSource()
    {
        if ($this->isJson()) {
            return $this->json();
        }

        return in_array($this->getRealMethod(), ['GET', 'HEAD']) ? $this->query : $this->request;
    }



    /**
     * Create an Illuminate request from a Symfony instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return static
     */
    public static function createFromBase(SymfonyRequest $request)
    {
        $newRequest = (new static)->duplicate(
            $request->query->all(), $request->request->all(), $request->attributes->all(),
            $request->cookies->all(), $request->files->all(), $request->server->all()
        );

        $newRequest->headers->replace($request->headers->all());

        $newRequest->content = $request->content;

        if ($newRequest->isJson()) {
            $newRequest->request = $newRequest->json();
        }

        return $newRequest;
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function duplicate(?array $query = null, ?array $request = null, ?array $attributes = null, ?array $cookies = null, ?array $files = null, ?array $server = null): static
    {
        return parent::duplicate($query, $request, $attributes, $cookies, $this->filterFiles($files), $server);
    }

    /**
     * Filter the given array of files, removing any empty values.
     *
     * @param  mixed  $files
     * @return mixed
     */
    protected function filterFiles($files)
    {
        if (! $files) {
            return;
        }

        foreach ($files as $key => $file) {
            if (is_array($file)) {
                $files[$key] = $this->filterFiles($files[$key]);
            }

            if (empty($files[$key])) {
                unset($files[$key]);
            }
        }

        return $files;
    }








    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        //$route = $this->route();

        //return Arr::has(
        //    $this->all() + ($route ? $route->parameters() : []),
        //    $offset
        //);
    }

    /**
     * Get the value at the given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->__get($offset);
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->getInputSource()->set($offset, $value);
    }

    /**
     * Remove the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->getInputSource()->remove($offset);
    }

    /**
     * Check if an input element is set on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return ! is_null($this->__get($key));
    }

    /**
     * Get an input element from the request.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        //return Arr::get($this->all(), $key, fn () => $this->route($key));
    }

}

