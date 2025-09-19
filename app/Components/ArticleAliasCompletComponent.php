<?php

namespace App\Components;

use App\Library\Components\BaseComponent;
use App\Components\ArticleCompletIDComponent;

/*
Exemple d'appel :
    <?= Component::articleComplet(5); ?>   <!-- équivalent à articleCompletID(5) -->
    <?= Component::articleComplet(0); ?>   <!-- dernier article -->
    <?= Component::articleComplet(-1); ?>  <!-- avant-dernier article -->
*/

class ArticleAliasCompletComponent extends BaseComponent
{
    /**
     * Rendu du composant alias
     *
     * @param array|int $params ID de l'article
     * @return string HTML de l'article
     */
    public function render(array|int $params = []): string
    {
        $arg = is_array($params) ? ($params[0] ?? 0) : $params;

        // On délègue à l'autre composant
        $articleComponent = new ArticleCompletIDComponent();
        
        return $articleComponent->render($arg);
    }
}
