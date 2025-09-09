<?php

namespace App\Http\Controllers\DevTest;

use Npds\View\View;
use ReflectionClass;
use App\Library\Database\Sql;
use App\Http\Controllers\Core\BaseController;


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

        //vd(
        //    Sql::fetch_assoc(Sql::query('SELECT * FROM ' . sql_prefix('users') . ' WHERE uid='. 2)),
        //    sql_fetch_assoc(sql_query('SELECT * FROM ' . sql_prefix('users') . ' WHERE uid='. 2))
        //);

        //vd('member_list', config('user.member_list'), config('user.member_list_toto'));

        return $this->createView()
            ->shares('title', 'Homepage')
            ->with('content', $content);
    }

}
