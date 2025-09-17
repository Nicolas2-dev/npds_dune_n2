<?php

namespace App\Http\Controllers\Core;

use App\Support\Facades\Block;
use App\Support\Facades\Theme;
use Npds\Support\Facades\View;
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
        View::share('theme', Theme::getTheme());

        View::share('pdst',  Block::checkPdst($this->pdst));

        parent::initialize();
    }


}
