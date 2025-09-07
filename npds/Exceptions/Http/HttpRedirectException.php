<?php

namespace System\Http\Exceptions;

use Exception;
use Npds\Exceptions\Http\HttpException;

class HttpRedirectException extends HttpException
{

    /**
     * L'URL de redirection
     *
     * @var string
     */
    private string $targetUrl;

    
    /**
     * Crée une nouvelle exception de redirection HTTP.
     *
     * @param int       $statusCode   Code de statut HTTP (par ex. 301, 302, 307)
     * @param string    $targetUrl    L'URL vers laquelle rediriger
     * @param string|null $message    Message optionnel
     * @param Exception|null $previous Exception précédente
     * @param int       $code         Code interne
     */
    public function __construct(
        int $statusCode,
        string $targetUrl,
        ?string $message = null,
        ?Exception $previous = null,
        int $code = 0
    ) {
        $this->targetUrl = $targetUrl;

        parent::__construct($statusCode, $message ?? "Redirect to {$targetUrl}", $previous, $code);
    }

    /**
     * Récupère l'URL de redirection.
     *
     * @return string
     */
    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }
    
}
