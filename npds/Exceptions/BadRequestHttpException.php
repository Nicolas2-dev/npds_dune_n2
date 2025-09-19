<?php

namespace Npds\Exceptions;

use Throwable;
use Npds\Exceptions\Http\HttpException;

class BadRequestHttpException extends HttpException
{

    public function __construct(string $message = 'Bad Request', int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}
