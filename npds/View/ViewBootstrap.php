<?php

namespace Npds\View;

use Npds\Config\Config;
use Npds\Events\Dispatcher;
use Npds\View\FileViewFinder;
use Npds\Filesystem\Filesystem;
use Npds\View\Engines\PhpEngine;
use Npds\View\Engines\FileEngine;
use Npds\View\Engines\CompilerEngine;
use Npds\View\Engines\EngineResolver;
use Npds\View\Compilers\TemplateCompiler;


class ViewBootstrap
{

    /**
     * Instance unique du singleton.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    /**
     * Instance de la factory de vues.
     *
     * @var Factory
     */
    protected Factory $factory;

   /**
     * Résolveur des moteurs de vues.
     *
     * @var EngineResolver
     */
    protected EngineResolver $resolver;

    /**
     * Finder des fichiers de vues.
     *
     * @var FileViewFinder
     */
    protected FileViewFinder $finder;


    /**
     * Constructeur : initialise le résolveur, le finder et la factory.
     */
    public function __construct()
    {
        $this->registerEngineResolver();
        $this->registerViewFinder();
        $this->registerFactory();
    }

    /**
     * Retourne l’instance unique du singleton.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Initialise le résolveur de moteurs de vues.
     *
     * @return void
     */
    public function registerEngineResolver(): void
    {
        $this->resolver = $resolver = new EngineResolver();

        foreach (array('php', 'template', 'file') as $engine) {
            $method = 'register' .ucfirst($engine) .'Engine';

            call_user_func(array($this, $method), $resolver);
        }
    }

    /**
     * Enregistre le moteur PHP simple.
     *
     * @param EngineResolver $resolver Le résolveur dans lequel enregistrer le moteur.
     */
    protected function registerPhpEngine(EngineResolver $resolver): void
    {
        $resolver->register('php', function () {
            return new PhpEngine();
        });
    }

    /**
     * Enregistre le moteur de templates compilés.
     *
     * @param EngineResolver $resolver Le résolveur dans lequel enregistrer le moteur.
     */
    protected function registerTemplateEngine(EngineResolver $resolver): void
    {
        $compiler = new TemplateCompiler(new Filesystem(), Config::get('view.compiled'));

        $resolver->register('template', function () use ($compiler) {
            return new CompilerEngine($compiler, new Filesystem());
        });
    }

    /**
     * Enregistre le moteur de vues "file" dans le résolveur d'engines.
     *
     * @param EngineResolver $resolver Instance du résolveur d'engines.
     * @return void
     */
    public function registerFileEngine(EngineResolver $resolver): void
    {
        $resolver->register('file', function()
        {
            return new FileEngine();
        });
    }

    /**
     * Initialise le finder des fichiers de vues.
     *
     * @return void
     */
    public function registerViewFinder(): void
    {
        $path = Config::get('view.paths', array());
        
        $this->finder = new FileViewFinder(new Filesystem(), $path);
    }

    /**
     * Initialise la factory de vues.
     *
     * @return void
     */
    public function registerFactory(): void
    {
        $this->factory = Factory::getInstance($this->resolver, $this->finder, new Dispatcher());
    }

    /**
     * Retourne le résolveur de moteurs de vues.
     *
     * @return EngineResolver
     */
    public function getEngine(): EngineResolver
    {
        return $this->resolver;
    }

    /**
     * Retourne le finder des fichiers de vues.
     *
     * @return FileViewFinder
     */
    public function getViewFinder(): FileViewFinder
    {
        return $this->finder;
    }

    /**
     * Retourne l'instance de la factory de vues.
     *
     * @return Factory
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }

}
