<?php

namespace Modules\Contact\Http\Controllers\Front;

use Npds\Support\Facades\View;
use App\Http\Controllers\Core\FrontBaseController;


class Contact extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {

        View::addNamespace('Modules/Contact', 'modules/Contact/Views');

        parent::initialize();        
    }

    /**
     * Affiche la page d'accueil
     *
     * @return View  Instance de la vue pour la page d'accueil
     */
    public function index()
    {
        $content = 'This is the Contact page';

        //include_once 'modules/contact/support/sform/contact.php';

        return $this->createView()
            ->shares('title', 'Contact')
            ->with('content', $content);
    }

}
