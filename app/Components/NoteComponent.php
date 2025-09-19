<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::note(); ?>
*/

class NoteComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        return "!delete!";
    }
}
