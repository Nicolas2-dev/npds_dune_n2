<?php

namespace Npds\Exceptions\Http;

use Throwable;
use Npds\Exceptions\Http\HttpException;

class UnauthorizedHttpException extends HttpException
{

    public function __construct(string $message = 'Unauthorized', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
