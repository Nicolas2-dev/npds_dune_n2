<?php

namespace App\Http\Controllers\Front;

use Npds\View\View;
use App\Http\Controllers\BaseController;

class Home extends BaseController
{

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
    public function index(): View
    {
        $content = 'This is the Homepage';

        return $this->createView()
            ->shares('title', 'Homepage')
            ->with('content', $content);
    }

}
