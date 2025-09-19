<?php

namespace App\Components;

use App\Support\Facades\Online;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::nbOnline(); ?>
*/

class NbOnlineComponent extends BaseComponent
{
    /**
     * Retourne le nombre d'utilisateurs en ligne
     *
     * @param array|string $params Paramètres optionnels (non utilisés ici)
     * @return string Nombre d'utilisateurs en ligne
     */
    public function render(array|string $params = []): string
    {
        [$nbOnline, $whoIm] = Online::WhoOnline();
        
        return (string)$nbOnline;
    }
}
