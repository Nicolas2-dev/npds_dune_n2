<?php

namespace Themes\Base\Library;


class Base
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    
    /**
     * Retourne l'instance singleton du dispatcher.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

}
