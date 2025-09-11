<?php

namespace App\Http\Controllers\Core;

use Npds\View\View;
use Npds\Http\Response;
use BadMethodCallException;
use Npds\Routing\Controller;

class BaseController extends Controller
{

    /**
     * Action courante exécutée par le contrôleur
     *
     * @var string|null
     */
    protected ?string $action = null;

    /**
     * Nom du layout à utiliser
     *
     * @var string
     */
    protected string $layout = 'Default';

    /**
     * Constructeur du contrôleur Home
     *
     * Permet d'initialiser le contrôleur, charger des services ou des middleware si nécessaire.
     */
    public function __construct()
    {
        //
    }

    /**
     * Exécute une méthode d'action du contrôleur avec les paramètres fournis
     *
     * @param  string  $method      Nom de la méthode d'action
     * @param  array   $parameters  Paramètres à passer à la méthode
     * @return Response             Réponse finale après before/after
     */
    public function callAction(string $method, array $parameters = []): Response
    {
        $this->action = $method;

        if (! is_null($response = $this->before())) {
            return $response;
        }

        $response = call_user_func_array(array($this, $method), $parameters);

        return $this->after($response);
    }

    /**
     * Méthode appelée avant l'exécution de l'action.
     * Permet d'intercepter la requête et de retourner une réponse si nécessaire.
     *
     * @return Response|mixed|null
     */
    protected function before(): mixed
    {
        // Aucun traitement par défaut
        return null;
    }

    /**
     * Méthode appelée après l'exécution de l'action.
     * Permet d'appliquer le layout si la réponse est une vue.
     *
     * @param  mixed  $response  Réponse renvoyée par l'action
     * @return Response          Réponse finale
     */
    protected function after(mixed $response): Response
    {
        if (($response instanceof View) && ! empty($this->layout)) {
            $layout = 'Layouts/' .$this->layout;

            $view = View::make($layout, array('content' => $response));

            return new Response($view->render(), 200);
        } else if (! $response instanceof Response) {
            $response = new Response($response);
        }

        return $response;
    }

    /**
     * Crée une instance de vue pour l'action courante
     *
     * @param  array       $data  Données à passer à la vue
     * @param  string|null $view  Nom de la vue (par défaut, action courante)
     * @return View               Instance de vue
     *
     * @throws BadMethodCallException Si le namespace du contrôleur est invalide
     */
    protected function createView(array $data = [], ?string $view = null): View
    {
        if (is_null($view)) {
            $view = ucfirst($this->action);
        }

        $classPath = str_replace('\\', '/', static::class);

        if (preg_match('#^App/Http/Controllers/(.*)/(.*)$#', $classPath, $matches) === 1) {
            $view = $matches[1] .'/' .$matches[2] .'/' .$view;
        } elseif(preg_match('#^Modules/(.*)/Http/Controllers/(.*)/(.*)$#', $classPath, $matches) === 1) {
            $view = 'modules/' .$matches[1] .'/' .$matches[2] .'/' .$view;
        }

        return View::make($view, $data);

        throw new BadMethodCallException('Invalid Controller namespace');
    }

}
