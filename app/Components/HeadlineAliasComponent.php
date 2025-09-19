<?php

namespace App\Components;

use App\Components\HeadlineIDComponent;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::headlineAlias(3); ?>
*/

class HeadlineComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $id = is_array($params) ? ($params[0] ?? '') : $params;

        // Appel direct du composant HeadlineIDComponent
        return (new HeadlineIDComponent())->render([$id]);
    }
}
