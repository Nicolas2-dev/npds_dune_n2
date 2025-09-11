<?php

namespace Modules\Contact\Http\Controllers\Front;

use Npds\View\View;
use App\Http\Controllers\Core\BaseController;


class Contact extends BaseController
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
        $content = 'This is the Contact page';

        //include_once 'modules/contact/support/sform/contact.php';

        return $this->createView()
            ->shares('title', 'Contact')
            ->with('content', $content);
    }

}
