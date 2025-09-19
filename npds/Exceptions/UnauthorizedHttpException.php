<?php

namespace Npds\Exceptions\Http;

use Throwable;
use Npds\Exceptions\Http\HttpException;

class UnauthorizedHttpException extends HttpException
{

    /**
     * Constructeur de l'exception "Unauthorized".
     *
     * Cette exception est levée lorsque l'accès à une ressource nécessite
     * une authentification et que l'utilisateur n'est pas autorisé (401).
     *
     * @param string         $message   Message décrivant l'erreur (par défaut 'Unauthorized').
     * @param int            $code      Code HTTP ou code d'erreur (par défaut 401).
     * @param Throwable|null $previous  Exception précédente, si elle existe.
     *
     * @return void
     */
    public function __construct(string $message = 'Unauthorized', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
