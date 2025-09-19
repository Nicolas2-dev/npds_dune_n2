<?php

namespace Npds\Exceptions;

use Exception;

class ForbiddenHttpException extends Exception
{

    protected int $statusCode = 403;


    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
}
