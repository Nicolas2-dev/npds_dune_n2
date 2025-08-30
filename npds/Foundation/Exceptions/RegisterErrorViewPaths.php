<?php

namespace Npds\Foundation\Exceptions;

use Npds\Support\Facades\View;

class RegisterErrorViewPaths
{
    /**
     * Register the error view paths.
     *
     * @return void
     */
    public function __invoke()
    {
        View::replaceNamespace('errors', collect(config('view.paths'))->map(function ($path) {
            return "{$path}/errors";
        })->push(__DIR__.'/views')->all());
    }
}
