<?php

namespace Npds\Exceptions\Http;

use Throwable;

class ServiceUnavailableHttpException extends HttpException
{

    /**
     * Constructeur de l'exception "Service Unavailable".
     *
     * Cette exception est levée lorsque le serveur ne peut pas traiter la
     * requête à cause d'une indisponibilité temporaire (503).
     *
     * @param string         $message   Message décrivant l'erreur (par défaut 'Service Unavailable').
     * @param int            $code      Code HTTP ou code d'erreur (par défaut 503).
     * @param Throwable|null $previous  Exception précédente, si elle existe.
     *
     * @return void
     */
    public function __construct(string $message = 'Service Unavailable', int $code = 503, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}