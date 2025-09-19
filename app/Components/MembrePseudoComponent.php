<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::membrePseudo(); ?>
*/

class MembrePseudoComponent extends BaseComponent
{
    /**
     * Retourne le pseudo de l'utilisateur connecté
     *
     * @param array|string $params Paramètres optionnels (non utilisés ici)
     * @return string Pseudo ou chaîne vide si non connecté
     */
    public function render(array|string $params = []): string
    {
        global $cookie;

        return $cookie[1] ?? '';
    }
}
