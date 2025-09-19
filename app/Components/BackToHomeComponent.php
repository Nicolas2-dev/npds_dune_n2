<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::backtohome(); ?>
*/

class BackToHomeComponent extends BaseComponent
{
    /**
     * Rend un bouton "Retour à l’accueil".
     *
     * @param array $params Optionnel, peut contenir 'label' ou 'url'
     *
     * @return string HTML
     */
    public function render(array $params = []): string
    {
        $label = $params['label'] ?? 'Retour à l’accueil';
        $url = $params['url'] ?? '/';

        return '<a href="' . htmlspecialchars($url) . '" class="btn btn-primary">' . htmlspecialchars($label) . '</a>';
    }
}
