<?php

namespace App\Components;

use App\Support\Sanitize;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::membreNom(); ?>
*/

class MembreNomComponent extends BaseComponent
{
    /**
     * Retourne le nom complet de l'utilisateur connecté
     *
     * @param array|string $params Paramètres optionnels (non utilisés ici)
     * @return string Nom complet ou vide si non connecté
     */
    public function render(array|string $params = []): string
    {
        global $cookie;

        if (isset($cookie[1])) {
            $uname = Sanitize::argFilter($cookie[1]);
            $rowQ = Q_select("SELECT name FROM " . sql_prefix('users') . " WHERE uname='$uname'", 3600);
            
            if (!empty($rowQ)) {
                return (string) $rowQ[0]['name'];
            }
        }

        return '';
    }
}
