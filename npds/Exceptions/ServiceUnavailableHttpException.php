<?php

namespace Npds\Exceptions\Http;

use Throwable;

class ServiceUnavailableHttpException extends HttpException
{

    public function __construct(string $message = 'Service Unavailable', int $code = 503, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}