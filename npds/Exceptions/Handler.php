<?php

namespace Npds\Exceptions;

use Exception;
use Throwable;
use ErrorException;
use Npds\Config\Config;
use App\Support\Facades\Theme;
use Npds\Support\Facades\View;
use App\Support\Facades\Mailer;
use Npds\Exceptions\Http\HttpException;
use Npds\Exceptions\FatalThrowableError;
use Npds\Exceptions\ForbiddenHttpException;
use Npds\Exceptions\BadRequestHttpException;
use App\Support\Facades\Assets as AssetManager;
use Npds\Exceptions\Http\NotFoundHttpException;
use Npds\Exceptions\Http\UnauthorizedHttpException;
use Npds\Exceptions\Http\MethodNotAllowedHttpException;
use Npds\Exceptions\Http\ServiceUnavailableHttpException;
use Npds\Exceptions\Http\InternalServerErrorHttpException;

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
     * Mapping des exceptions vers code, vue et titre.
     *
     * @var array<class-string, array{code:int, view:string, title:string}>
     */
    protected array $exceptionMap = [
        NotFoundHttpException::class => [
            'code'  => 404,
            'view'  => 'Exceptions/404',
            'title' => 'Page non trouvée',
        ],
        ForbiddenHttpException::class => [
            'code'  => 403,
            'view'  => 'Exceptions/403',
            'title' => 'Accès interdit',
        ],
        BadRequestHttpException::class => [
            'code'  => 400,
            'view'  => 'Exceptions/400',
            'title' => 'Requête incorrecte',
        ],
        UnauthorizedHttpException::class => [
            'code'  => 401,
            'view'  => 'Exceptions/401',
            'title' => 'Non autorisé',
        ],
        MethodNotAllowedHttpException::class => [
            'code'  => 405,
            'view'  => 'Exceptions/405',
            'title' => 'Méthode non autorisée',
        ],
        InternalServerErrorHttpException::class => [
            'code'  => 500,
            'view'  => 'Exceptions/500',
            'title' => 'Erreur interne du serveur',
        ],
        ServiceUnavailableHttpException::class => [
            'code'  => 503,
            'view'  => 'Exceptions/503',
            'title' => 'Service indisponible',
        ],
    ];


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

        //if (! $e instanceof HttpException) {
        //    $this->report($e);
        //}

        if (!($e instanceof NotFoundHttpException || $e instanceof ForbiddenHttpException)) {
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
        // Envoi mail si mode production
        if ($this->debug) {

            $email   = Config::get('mailer.adminmail');
            $subject = "Erreur sur le site : " . $e->getMessage();
            $message = $this->formatMessage($e);
            $headers = Config::get('mailer.domainemail');
            
            Mailer::sendEmail($email, $subject, $message, $headers);
        }
    }

    /**
     * Formate les informations d'une exception en une chaîne lisible pour le journal ou l'envoi par mail.
     *
     * Cette méthode extrait la date, le message, le code, le fichier, la ligne et la trace de l'exception.
     *
     * @param Exception $e L'exception à formater.
     *
     * @return string Une chaîne contenant les informations formatées de l'exception.
     */
    protected function formatMessage(Exception $e): string
    {
        return sprintf(
            "Date: %s\nMessage: %s\nCode: %s\nFile: %s\nLine: %s\nTrace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getCode(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
    }

    /**
     * Affiche une exception à l’utilisateur.
     *
     * @param Exception $e
     *
     * @return void
     */
    public function render(Exception $e): void
    {
        // Assets Register
        AssetManager::register();

        $info = $this->getStatusCodeFromException($e);

        $theme = Theme::getTheme();    
           
        View::addNamespace('Themes/' . $theme, 'themes/' . $theme .'/Views');

        $view = View::make('Themes/' . $theme .'::Layouts/Default')
            ->shares('title', $info['title'])
            ->shares('pdst', 0)
            ->nest('content', $info['view'], ['exception' => $e]);

        echo $view->render();

        exit();
    }

    /**
     * Détermine automatiquement le code HTTP depuis l’exception
     */
    protected function getStatusCodeFromException(Exception $e): string|array
    {
        foreach ($this->exceptionMap as $class => $info) {
            if ($e instanceof $class) {
                return $info;
            }
        }

        // HttpException non listée
        if ($e instanceof HttpException) {
            return [
                'code'  => $e->getStatusCode(),
                'view'  => 'Exceptions/Default',
                'title' => 'Erreur Npds',
            ];
        }

        // Autres exceptions (PHP fatales, fonction inexistante, etc.)
        return [
            'code'  => $this->debug ? 'Debug' : 500,
            'view'  => $this->debug ? 'Exceptions/Debug' : 'Exceptions/500',
            'title' => 'Erreur interne du serveur',
        ];
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
