<?php

namespace Npds\Http\Concerns;

use Npds\Support\Str;

trait InteractsWithContentTypes
{


    /**
     * Déterminer si la requête envoie du JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        return Str::contains($this->header('CONTENT_TYPE') ?? '', ['/json', '+json']);
    }





}
