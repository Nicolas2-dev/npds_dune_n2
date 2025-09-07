<?php

namespace Npds\Application\Exceptions;

use ErrorException;
use ParseError;
use ReflectionProperty;
use Throwable;
use TypeError;

class FatalThrowableError extends ErrorException
{

    /**
     * Crée une exception fatale à partir d’un Throwable.
     *
     * @param Throwable $e L’exception ou erreur à convertir
     */
    public function __construct(Throwable $e)
    {
        if ($e instanceof ParseError) {
            $message = 'Parse error: ' .$e->getMessage();
            $severity = E_PARSE;
        } else if ($e instanceof TypeError) {
            $message = 'Type error: ' .$e->getMessage();
            $severity = E_RECOVERABLE_ERROR;
        } else {
            $message = $e->getMessage();
            $severity = E_ERROR;
        }

        ErrorException::__construct(
            $message,
            $e->getCode(),
            $severity,
            $e->getFile(),
            $e->getLine()
        );

        $this->setTrace($e->getTrace());
    }

    /**
     * Définit la pile d’appels (trace) de l’exception.
     *
     * @param array $trace La pile d’appels provenant de l’exception d’origine
     *
     * @return void
     */
    protected function setTrace(array $trace): void
    {
        $traceReflector = new ReflectionProperty('Exception', 'trace');

        $traceReflector->setAccessible(true);

        $traceReflector->setValue($this, $trace);
    }

}
