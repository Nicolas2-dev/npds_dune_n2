<?php

namespace App\Http\Controllers\DevTest;

use Npds\View\View;
use ReflectionClass;
use App\Library\Database\Sql;
use App\Http\Controllers\Core\FrontBaseController;


class Home extends FrontBaseController
{

    
    protected int $pdst = 0;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * Affiche la page d'accueil
     *
     * @return View  Instance de la vue pour la page d'accueil
     */
    public function index()
    {
        $content = 'This is the Homepage dev test';

        // test erreur !
        toto();

        //vd(
        //    Sql::fetch_assoc(Sql::query('SELECT * FROM ' . sql_prefix('users') . ' WHERE uid='. 2)),
        //    sql_fetch_assoc(sql_query('SELECT * FROM ' . sql_prefix('users') . ' WHERE uid='. 2))
        //);

        //vd('member_list', config('user.member_list'), config('user.member_list_toto'));

        return $this->createView()
            ->shares('title', 'Homepage de dev test')
            ->with('content', $content);
    }

}
