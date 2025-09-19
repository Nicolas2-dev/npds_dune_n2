<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

// Note faire une lib Headlin + trait

/*
Exemple d'appel :
    <?= Component::headlineID(3); ?>
*/

class HeadlineIDComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $id = is_array($params) ? ($params[0] ?? '') : $params;

        // Appel natif à la fonction NPDS
        return @Headline::headlines($id, '');
    }
}
