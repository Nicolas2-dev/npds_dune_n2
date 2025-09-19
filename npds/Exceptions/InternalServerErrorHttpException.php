<?php

namespace Npds\Exceptions\Http;

use Throwable;
use Npds\Exceptions\Http\HttpException;

class InternalServerErrorHttpException extends HttpException
{

    /**
     * Constructeur de l'exception Internal Server Error.
     *
     * @param string         $message   Message décrivant l'erreur (par défaut 'Internal Server Error').
     * @param int            $code      Code HTTP ou code d'erreur (par défaut 500).
     * @param Throwable|null $previous  Exception précédente, si elle existe.
     *
     * @return void
     */
    public function __construct(string $message = 'Internal Server Error', int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}