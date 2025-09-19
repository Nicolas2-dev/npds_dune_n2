<?php

use App\Support\Facades\Online;
use App\Library\Components\BaseComponent;

/*
<?= Component::whoIm(); ?>
*/

class WhoImComponent extends BaseComponent
{
    /**
     * Retourne la liste des utilisateurs en ligne
     *
     * @param array|string $params Paramètres optionnels (non utilisés ici)
     * @return string Liste des utilisateurs en ligne
     */
    public function render(array|string $params = []): string
    {
        [$nbOnline, $whoIm] = Online::WhoOnline();

        return (string)$whoIm;
    }
}
