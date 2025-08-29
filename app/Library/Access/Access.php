<?php

namespace App\Library\Access;


class Access
{

    /**
     * Affiche la page d'accès refusé et termine l'exécution.
     *
     * @return void
     */
    public static function access_denied(): void
    {
        include 'admin/die.php';
    }

}
