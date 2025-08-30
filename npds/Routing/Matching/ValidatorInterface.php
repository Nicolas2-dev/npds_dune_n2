<?php

namespace Npds\Routing\Matching;

use Npds\Http\Request;
use Npds\Routing\Route;

interface ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  \Npds\Routing\Route  $route
     * @param  \Npds\Http\Request  $request
     * @return bool
     */
    public function matches(Route $route, Request $request);
}
