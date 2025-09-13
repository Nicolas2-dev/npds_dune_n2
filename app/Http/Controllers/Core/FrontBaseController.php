<?php

namespace App\Http\Controllers\Core;

use Npds\View\View;
use App\Support\Facades\Block;
use App\Support\Facades\Theme;
use Npds\Support\Facades\Views;
use App\Http\Controllers\Core\BaseController;


class FrontBaseController extends BaseController
{


    protected int $pdst = 1;

    /**
     * Constructeur du contrôleur Home
     *
     * Permet d'initialiser le contrôleur, charger des services ou des middleware si nécessaire.
     */
    protected function initialize()
    {
        //
        Views::share('theme', Theme::getTheme());

        Views::share('pdst',  Block::checkPdst($this->pdst));

        parent::initialize();
    }


}
