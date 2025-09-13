<?php

namespace Npds\View\Engines;


abstract class Engine
{

    /**
     * La vue qui a été rendue en dernier lieu.
     *
     * @var string|null
     */
    protected ?string $lastRendered = null;

    /**
     * Obtenez la dernière vue rendue.
     *
     * @return string|null
     */
    public function getLastRendered(): ?string
    {
        return $this->lastRendered;
    }

}
