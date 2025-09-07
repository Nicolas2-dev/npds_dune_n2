<?php

namespace Npds\Exceptions\Http;

use Throwable;
use Npds\Exceptions\Http\HttpException;

class NotFoundHttpException extends HttpException
{

    /**
     * Constructeur pour l'exception 404.
     *
     * @param string|null    $message  Message d'erreur optionnel
     * @param Throwable|null $previous Exception précédente
     * @param int            $code     Code interne optionnel
     */
    public function __construct(?string $message = null, ?Throwable $previous = null, int $code = 0)
    {
        parent::__construct(404, $message, $previous, $code);
    }

}
