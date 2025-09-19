<?php

namespace Npds\Exceptions;

use Throwable;
use Npds\Exceptions\Http\HttpException;

class BadRequestHttpException extends HttpException
{

    /**
     * Constructeur de l'exception.
     *
     * @param string         $message   Message décrivant l'erreur (par défaut 'Bad Request').
     * @param int            $code      Code HTTP ou code d'erreur (par défaut 400).
     * @param Throwable|null $previous  Exception précédente, si elle existe.
     *
     * @return void
     */
    public function __construct(string $message = 'Bad Request', int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}
