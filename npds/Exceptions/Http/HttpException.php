<?php

namespace Npds\Exceptions\Http;

use RuntimeException;

class HttpException extends \RuntimeException
{

    /**
     * Code HTTP de l'exception.
     *
     * @var int
     */
    private int $statusCode;


    /**
     * Constructeur de l'exception HTTP.
     *
     * @param int            $statusCode Code HTTP (ex: 404, 500)
     * @param string|null    $message    Message d'erreur
     * @param \Throwable|null $previous  Exception précédente
     * @param int            $code       Code interne de l'exception
     */
    public function __construct(int $statusCode, ?string $message = null, ?\Throwable $previous = null, int $code = 0)
    {
        $this->statusCode = $statusCode;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Retourne le code HTTP associé à l'exception.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

}
