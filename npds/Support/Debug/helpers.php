<?php

use Npds\Support\Debug\Debug;

if (! function_exists('dd'))
{
    /**
     * Videz les variables transmises et terminez le script.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        array_map(function ($value)
        {
            with(new Debug)->dump($value);

        }, func_get_args());

        die (1);
    }
}

if (! function_exists('vd'))
{
    /**
     * Videz les variables transmises et continue le script.
     *
     * @param  mixed
     * @return void
     */
    function vd()
    {
        array_map(function ($value)
        {
            with(new Debug)->dump($value);

        }, func_get_args());
    }
}
