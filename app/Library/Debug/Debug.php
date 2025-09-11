<?php

namespace App\Library\Debug;

use Npds\Config\Config;
use InvalidArgumentException;


class Debug 
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
    
    /**
     * Initialise le mode debug selon la configuration.
     * 
     * - Si type = 'php' : active le niveau d'erreur PHP.
     * - Si type = 'whoops' : initialise Whoops avec ou sans PrettyPageHandler.
     *
     * @return void
     */
    public function initDebug(): void
    {
        $type   = Config::get('debug.type', 'php');
        $debug  = Config::get('debug.debug', false);

        if ($debug && $type === 'php') {
            $level = Config::get('debug.level', 0);

            static::setErrorReporting($level);

        } elseif ($debug && $type === 'whoops') {
            $prettyPage = Config::get('debug.whoops.pretty_page', true);

            static::initWhoops($prettyPage);
        }
    }

    /**
     * Configure le niveau de rapport d'erreurs selon un niveau simple.
     *
     * @param string|int $level Niveau d'erreur : 'none', 'dev', 'standard', 'all' ou valeur int.
     * @return void
     */
    private static function setErrorReporting(string|int $level): void
    {
        $reporting = match($level) {
            'none'     => 0,
            'dev'      => E_ERROR | E_WARNING | E_PARSE | E_NOTICE,
            'standard' => E_ERROR | E_WARNING | E_PARSE,
            'all'      => E_ALL,
            default    => is_int($level) ? $level : throw new InvalidArgumentException('Niveau d\'erreur inconnu : '. $level),
        };

        error_reporting($reporting);
    }

    /**
     * Initialise Whoops pour gérer les erreurs et exceptions.
     *
     * @param bool $prettyPage Active le handler PrettyPage si true, sinon simple handler.
     * @return void
     */
    private static function initWhoops(bool $prettyPage = true): void
    {
        $whoops = new \Whoops\Run();

        if ($prettyPage) {
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        } else {
            $whoops->pushHandler(function ($exception) {
                echo 'Une erreur est survenue : ' . $exception->getMessage();
            });
        }

        $whoops->register();
    }

}
