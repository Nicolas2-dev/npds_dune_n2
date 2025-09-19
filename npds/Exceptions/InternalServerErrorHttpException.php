<?php

namespace Npds\Exceptions\Http;

use Throwable;
use Npds\Exceptions\Http\HttpException;

class InternalServerErrorHttpException extends HttpException
{

    public function __construct(string $message = 'Internal Server Error', int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}