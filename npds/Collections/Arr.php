<?php

namespace Npds\Collections;

use ArgumentCountError;
use ArrayAccess;
use Npds\Support\Traits\Macroable;
use InvalidArgumentException;

class Arr
{














    /**
     * Filter the array using the given callback.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     */
    public static function where($array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }


}
