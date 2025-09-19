<?php

namespace Npds\Exceptions\Http;

use Throwable;
use Npds\Exceptions\Http\HttpException;

class MethodNotAllowedHttpException extends HttpException
{

    public function __construct(string $message = 'Method Not Allowed', int $code = 405, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}