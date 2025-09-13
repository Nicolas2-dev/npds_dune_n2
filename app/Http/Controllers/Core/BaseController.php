<?php

namespace App\Http\Controllers\Core;

use Npds\Support\Str;
use Npds\Http\Request;
use Npds\Config\Config;
use Npds\Http\Response;
use BadMethodCallException;
use Npds\Routing\Controller;
use Npds\View\ViewBootstrap;
use Npds\Support\Facades\Views;
use App\Support\Facades\Assets as AssetManager;
use Npds\Support\Contracts\RenderableInterface;


class BaseController extends Controller
{


    protected Request $request;


    protected ?string $action = null;


    protected array $parameters = [];


    protected $layout = 'Default';


    protected $autoRender = false;


    protected $autoLayout = true;


    protected $viewPath;


    protected $viewData = array();


    protected $theme;


    protected $skin;



    public function __construc(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Constructeur du contrôleur Home
     *
     * Permet d'initialiser le contrôleur, charger des services ou des middleware si nécessaire.
     */
    protected function initialize()
    {
        // Configurez le thème utilisé par défaut, s'il n'est pas déjà défini.
        if (is_null($this->theme)) {
            $this->theme = Config::get('theme.Default_Theme', 'Themes/Base');
        }

        if (is_null($this->skin)) {
            $this->skin = Config::get('theme.Default_Skin', 'default');
        }

        $theme = $this->getTheme();

        //$this->setThemeConfig($theme);

        // provisoire le temps de finir les autres parties
        Views::addNamespace('App', 'app');
        Views::addNamespace('Themes/NpdsBoostSK', 'themes/NpdsBoostSK/Views');
        Views::addNamespace('Themes/Base', 'themes/Base/Views');
        
        //
        Config::set('themes.current.base', $theme);
        Config::set('themes.current.skin', $this->skin);

        if ($theme === false) {
            return '';
        }

        // Un thème est configuré pour ce contrôleur.
        else if (! Str::contains($theme, '/')) {
            $theme = 'Themes/' .$theme;
        }

        Views::overridesFrom($theme);

        // Assets Register
        AssetManager::register();
    }

    /**
     * Exécuter une action sur le contrôleur.
     *
     * @param string  $method
     * @param array   $params
     * @param \Two\Http\Request  $request
     * @return mixed
     */
    public function callAction(string $method, array $parameters)
    {
        $this->action  = $method;

        $this->parameters = $parameters;

        //
        $this->initialize();

        $response = call_user_func_array(array($this, $method), $parameters);

        if (is_null($response) && $this->autoRender()) {
            $response = $this->createView();
        }

        //
        //$this->resolveMetatags();

        return $this->processResponse($response);
    }

    /**
     * Traiter une réponse d'action du contrôleur.
     *
     * @param  mixed   $response
     * @return mixed
     */
    protected function processResponse($response)
    {
        if (! $response instanceof RenderableInterface) {
            return $response;
        }

        // La réponse est une implémentation RenderableInterface.
        else if (! empty($view = $this->resolveLayoutView()) && $this->autoLayout()) {
            return Views::make($view, $this->viewData)->with('content', $response);
        }
    }

    /**
     * Obtient un nom de vue localisé pour la mise en page actuellement utilisée.
     *
     * @return string
     */
    protected function resolveLayoutView()
    {
        if (empty($layout = $this->getLayout())) {
            return false;
        }

        //$direction = Language::direction();

        //if ($direction == 'rtl') {
        //    $view = $this->resolveViewFromTheme('Layouts/RTL/' .$layout);
        //
        //    if (View::exists($view)) {
        //        return $view;
        //    }
        //}

        //return 'Layouts/' .$layout;
        return $this->resolveViewFromTheme('Layouts/' .$layout);
    }

    /**
     * Obtient un nom de vue qualifié pour la mise en page implicite ou donnée.
     *
     * @param  string  $view
     * @return string
     */
    protected function resolveViewFromTheme($view)
    {
        if (empty($theme = Config::get('themes.current.theme', $this->getTheme()))) {
            return $view;
        }

        // Un thème est spécifié pour le rendu automatique.
        else if (! Str::contains($theme, '/')) {
            return sprintf('Themes/%s::%s', $theme, $view);
        }

        return sprintf('%s::%s', $theme, $view);
    }

    /**
     * Créez une instance de vue pour le nom de vue implicite (ou spécifié).
     *
     * @param  array  $data
     * @param  string|null  $view
     * @return \Two\View\View
     */
    protected function createView(array $data = array(), $view = null)
    {
        if (is_null($view)) {
            $view = ucfirst($this->action);
        }

        $view = sprintf('%s/%s', $this->resolveViewPath(), $view);

        return Views::make($view, array_merge($this->viewData, $data));
    }

    /**
     * Obtient un chemin d'accès View qualifié.
     *
     * @return string
     * @throws \BadMethodCallException
     */
    protected function resolveViewPath()
    {
        if (isset($this->viewPath)) {
            return $this->viewPath;
        }

        $path = str_replace('\\', '/', static::class);

        if (preg_match('#^(.+)/Http/Controllers/(.*)$#', $path, $matches) === 1) {
            list (, $basePath, $viewPath) = $matches;

            //
            if ($basePath != 'App') {
                $viewPath = sprintf('%s::%s', $basePath, $viewPath);
            }

            return $this->viewPath = $viewPath;
        }

        throw new BadMethodCallException('Invalid controller namespace');
    }

    /**
     * Ajoutez une paire clé/valeur aux données de la vue.
     *
     * Bound data will be available to the view as variables.
     *
     * @param  string|array  $one
     * @param  string|array  $two
     * @return BaseController
     */
    public function set($one, $two = null)
    {
        if (is_array($one)) {
            $data = is_array($two) ? array_combine($one, $two) : $one;
        } else {
            $data = array($one => $two);
        }

        $this->viewData = $data + $this->viewData;

        return $this;
    }

    /**
     * Active ou désactive le mode conventionnel de rendu automatique de Two.
     *
     * @param bool|null  $enable
     * @return bool
     */
    public function autoRender($enable = null)
    {
        if (is_null($enable)) {
            return $this->autoRender;
        }

        $this->autoRender = (bool) $enable;

        return $this;
    }

    /**
     * Active ou désactive le mode conventionnel d'application des fichiers de mise en page de Two.
     *
     * @param bool|null  $enable
     * @return bool
     */
    public function autoLayout($enable = null)
    {
        if (is_null($enable)) {
            return $this->autoLayout;
        }

        $this->autoLayout = (bool) $enable;

        return $this;
    }

    /**
     * Renvoie l'instance Request actuelle.
     *
     * REMARQUE : ces informations sont disponibles après l'appel de l'action.
     *
     * @return \Two\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Renvoie l'action appelée en cours
     *
     * REMARQUE : ces informations sont disponibles après l'appel de l'action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Renvoie les paramètres d'appel en cours
     *
     * REMARQUE : ces informations sont disponibles après l'appel de l'action.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Renvoie le thème actuel.
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Renvoie la mise en page actuelle.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Renvoie les données de la vue actuelle.
     *
     * @return string
     */
    public function getViewData()
    {
        return $this->viewData;
    }

    /**
     * Exécute une méthode d'action du contrôleur avec les paramètres fournis
     *
     * @param  string  $method      Nom de la méthode d'action
     * @param  array   $parameters  Paramètres à passer à la méthode
     * @return Response             Réponse finale après before/after
     */
    //public function callAction(string $method, array $parameters = []): Response
    //{
    //    $this->action = $method;
    //
    //    if (! is_null($response = $this->before())) {
    //        return $response;
    //    }
    //
    //    $response = call_user_func_array(array($this, $method), $parameters);
    //
    //    return $this->after($response);
    //}

    /**
     * Méthode appelée avant l'exécution de l'action.
     * Permet d'intercepter la requête et de retourner une réponse si nécessaire.
     *
     * @return Response|mixed|null
     */
    //protected function before(): mixed
    //{
    //    // Assets Register
    //    AssetManager::register();
    //
    //    // Aucun traitement par défaut
    //    return null;
    //}

    /**
     * Méthode appelée après l'exécution de l'action.
     * Permet d'appliquer le layout si la réponse est une vue.
     *
     * @param  mixed  $response  Réponse renvoyée par l'action
     * @return Response          Réponse finale
     */
    //protected function after(mixed $response): Response
    //{
    //    if (($response instanceof View) && ! empty($this->layout)) {
    //        $layout = 'Layouts/' .$this->layout;
    //
    //        $view = View::make($layout, array('content' => $response));
    //
    //        return new Response($view->render(), 200);
    //    } else if (! $response instanceof Response) {
    //        $response = new Response($response);
    //    }
    //
    //    return $response;
    //}

    /**
     * Crée une instance de vue pour l'action courante
     *
     * @param  array       $data  Données à passer à la vue
     * @param  string|null $view  Nom de la vue (par défaut, action courante)
     * @return View               Instance de vue
     *
     * @throws BadMethodCallException Si le namespace du contrôleur est invalide
     */
    //protected function createView(array $data = [], ?string $view = null): View
    //{
    //    if (is_null($view)) {
    //        $view = ucfirst($this->action);
    //    }
    //
    //    $classPath = str_replace('\\', '/', static::class);
    //
    //    if (preg_match('#^App/Http/Controllers/(.*)/(.*)$#', $classPath, $matches) === 1) {
    //        $view = $matches[1] .'/' .$matches[2] .'/' .$view;
    //    } elseif(preg_match('#^Modules/(.*)/Http/Controllers/(.*)/(.*)$#', $classPath, $matches) === 1) {
    //        $view = 'modules/' .$matches[1] .'/' .$matches[2] .'/' .$view;
    //    }
    //
    //    return View::make($view, $data);
    //
    //    throw new BadMethodCallException('Invalid Controller namespace');
    //}

}
