<?php

namespace App\Library\Access;


class Access
{

    /**
     * Affiche la page d'accès refusé et termine l'exécution.
     *
     * @return void
     */
    public static function accessDenied(): void
    {
        include 'admin/die.php';
    }
}
