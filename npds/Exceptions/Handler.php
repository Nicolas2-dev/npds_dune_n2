<?php

namespace Npds\Exceptions;

use Exception;
use Throwable;
use ErrorException;
use Npds\Config\Config;
use App\Support\Facades\Theme;
use Npds\Support\Facades\View;
use Npds\Exceptions\Http\HttpException;
use Npds\Exceptions\FatalThrowableError;
use App\Support\Facades\Assets as AssetManager;

class Handler
{

    /**
     * Instance unique du gestionnaire.
     *
     * @var self|null
     */
    protected static ?Handler $instance = null;

    /**
     * Indique si le mode debug est actif.
     *
     * @var bool
     */
    protected bool $debug = false;

    
    /**
     * Initialise l’instance du gestionnaire et configure le mode debug.
     */
    public function __construct()
    {
        $this->debug = Config::get('debug.debug_handler', true);
    }

    /**
     * Initialise le gestionnaire d’exceptions global.
     *
     * Configure les handlers pour les erreurs, exceptions et shutdown.
     *
     * @return void
     */
    public static function initialize(): void
    {
        static::$instance = $instance = new static();

        // Configure les gestionnaires d’exceptions.
        set_error_handler(array($instance, 'handleError'));

        set_exception_handler(array($instance, 'handleException'));

        register_shutdown_function(array($instance, 'handleShutdown'));
    }

    /**
     * Transforme les erreurs PHP en exceptions.
     *
     * @param int $level Niveau de l’erreur
     * @param string $message Message de l’erreur
     * @param string $file Fichier où l’erreur est survenue
     * @param int $line Ligne où l’erreur est survenue
     * @param array $context Contexte de l’erreur
     *
     * @return void
     *
     * @throws \ErrorException
     */
    public function handleError(int $level, string $message, string $file = '', int $line = 0, array $context = []): void
    {
        if (error_reporting() & ($level > 0)) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Gère les exceptions non capturées.
     *
     * @param Throwable $e
     *
     * @return void
     */
    public function handleException(Throwable $e): void
    {
        if (! $e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }

        if (! $e instanceof HttpException) {
            $this->report($e);
        }

        $this->render($e);
    }

    /**
     * Rapporte une exception (log, monitoring, etc.).
     *
     * @param Exception $e
     *
     * @return void
     */
    public function report(Exception $e): void
    {
        // 
    }

    /**
     * Affiche une exception à l’utilisateur.
     *
     * @param Exception $e
     *
     * @return void
     */
    public function render(Exception $e)
    {
        $type = $this->debug ? 'Debug' : 'Default';

        $theme = Theme::getTheme();

        // Assets Register
        AssetManager::register();

        View::addNamespace('Themes/' . $theme, 'themes/' . $theme .'/Views');

        $view = View::make('Themes/' . $theme .'::Layouts/Default')
            ->shares('title', 'Erreur Npds !')
            ->shares('pdst', 0)
            ->nest('content', 'Exceptions/' .$type, array('exception' => $e));

        echo $view->render();
    }

    /**
     * Gère les erreurs fatales lors de l’arrêt du script.
     *
     * @return void
     */
    public function handleShutdown(): void
    {
        if (! is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalExceptionFromError($error));
        }
    }

    /**
     * Convertit une erreur fatale en exception.
     *
     * @param array $error Tableau d’erreur retourné par error_get_last()
     *
     * @return ErrorException
     */
    protected function fatalExceptionFromError(array $error): ErrorException
    {
        return new ErrorException(
            $error['message'], $error['type'], 0, $error['file'], $error['line']
        );
    }

    /**
     * Détermine si un type d’erreur est fatal.
     *
     * @param int $type
     *
     * @return bool
     */
    protected function isFatal(int $type): bool
    {
        return in_array($type, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE));
    }

}
