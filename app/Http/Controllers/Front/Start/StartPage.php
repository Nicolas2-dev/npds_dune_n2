<?php

namespace App\Http\Controllers\Front\Start;


use Npds\View\View;
use Npds\Config\Config;
use App\Library\Auth\Auth;
use Npds\Support\Facades\Redirect;
use Npds\Http\Response as HttpResponse;
use App\Http\Controllers\BaseController;

class StartPage extends BaseController
{

    /**
     * Liste des urls autorisées pour la page d'accueil.
     *
     * @var [type]
     */
    private const ALLOWED_OP = [
        'index', 
        'edito', 
        'edito-nonews'
    ];


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
     * Affiche la page d'accueil
     *
     * @return View  Instance de la vue pour la page d'accueil
     */
    public function index(?string $start = null): View|HttpResponse
    {
        $Start_Page = Config::get('app.Start_Page');

        if (!Auth::AutoReg()) {
            global $user;
        
            unset($user);
        }

        $start = $start ? rtrim($start, '/') : '';

        if (in_array($start, self::ALLOWED_OP, true)) {
            return $this->theindex($start);
        } else {
            return Redirect::to($Start_Page);
        }
    }

    /**
     * Génère la vue principale de l’index.
     *
     * @param string   $start    Point d’entrée ou identifiant de la section à afficher.
     * @param int|null $catid    Identifiant optionnel de catégorie (par défaut 0).
     * @param int|null $marqeur  Identifiant optionnel de marqueur (par défaut 0).
     *
     * @return View   Instance de la vue générée pour l’index.
     */
    private function theindex(string $start, ?int $catid = 0, ?int $marqeur = 0): View
    {
        $content = "$start, $catid, $marqeur";

        return $this->createView(['content' => $content], 'theindex')
            ->shares('title', 'Homepage')
            ->with('contentw', 'test with')
            ->with('Start_Page', Config::get('app.Start_Page'));
    }

}
