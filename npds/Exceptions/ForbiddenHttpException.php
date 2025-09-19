<?php

namespace Npds\Exceptions;

use Exception;

class ForbiddenHttpException extends Exception
{

    /**
     * Code HTTP associé à l'exception.
     *
     * @var int
     */
    protected int $statusCode = 403;


    /**
     * Récupère le code HTTP de l'exception.
     *
     * @return int Code HTTP (403 dans ce cas)
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
}
