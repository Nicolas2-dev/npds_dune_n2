<?php

use Npds\Config\Config;
use Npds\Exceptions\Http\HttpException;
use System\Http\Exceptions\HttpRedirectException;

if (! function_exists('abort'))
{
    /**
     * Arrête l’exécution de l’application avec une exception HTTP.
     *
     * @param int  code
     * @param string  $message
     * @return string
     */
    function abort($code = 404, $message = null)
    {
        throw new HttpException($code, $message);
    }
}

if (! function_exists('redirect_back'))
{
    /**
     * Arrêter l’exécution de l’application en lançant une HttpRedirectException.
     *
     * @param int  code
     * @param string  $message
     * @return void
     */
    function redirect_back($code = 301, $message = null)
    {
        $url = ! empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url();

        throw new HttpRedirectException($url, $code, $message);
    }
}

if (! function_exists('last')) {
    /**
     * Retourne le dernier élément d’un tableau ou null si vide.
     *
     * @param array $array
     * @return mixed|null
     */
    function last(array $array) {
        return empty($array) ? null : end($array);
    }
}

if (! function_exists('site_url'))
{
    /**
     * Site URL helper.
     *
     * @param string $path
     * @return string
     */
    function site_url($path = '/')
    {
        return Config::get('app.url') .ltrim($path, '/');
    }
}

if (! function_exists('asset_url'))
{
    /**
     * Assistant d'URL d'élément
     *
     * @param string $path
     * @return string
     */
    function asset_url($path)
    {
        $path = ltrim($path, '/');

        return site_url('assets/' .$path);
    }
}

if (! function_exists('config'))
{
    /**
     * Récupère la valeur d'une clé de configuration.
     *
     * @param string $key
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed
     */
    function config(string $key, mixed $default = null): mixed
    {
        return Config::has($key) ? Config::get($key) : $default;
    }
}

if (! function_exists('with'))
{
    /**
     * Renvoie l'objet donné. Utile pour enchaîner.
     *
     * @param  mixed  $object
     * @return mixed
     */
    function with($object)
    {
        return $object;
    }
}
