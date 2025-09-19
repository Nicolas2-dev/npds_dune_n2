<?php

namespace Npds\Exceptions\Http;

use Throwable;
use Npds\Exceptions\Http\HttpException;

class MethodNotAllowedHttpException extends HttpException
{

    /**
     * Constructeur de l'exception "Method Not Allowed".
     *
     * Cette exception est levée lorsque la méthode HTTP utilisée pour accéder
     * à une ressource n'est pas autorisée.
     *
     * @param string         $message   Message décrivant l'erreur (par défaut 'Method Not Allowed').
     * @param int            $code      Code HTTP ou code d'erreur (par défaut 405).
     * @param Throwable|null $previous  Exception précédente, si elle existe.
     *
     * @return void
     */
    public function __construct(string $message = 'Method Not Allowed', int $code = 405, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}