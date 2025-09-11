<?php

namespace App\Http\Controllers\Core;

use Npds\View\View;
use App\Support\Facades\Block;
use App\Support\Facades\Theme;
use App\Http\Controllers\Core\BaseController;


class FrontBaseController extends BaseController
{


    protected int $pdst = 1;

    /**
     * Constructeur du contrôleur Home
     *
     * Permet d'initialiser le contrôleur, charger des services ou des middleware si nécessaire.
     */
    public function __construct()
    {
        //
        View::share('theme', Theme::getTheme());

        View::share('pdst',  Block::checkPdst($this->pdst));

        parent::__construct();
    }


}
