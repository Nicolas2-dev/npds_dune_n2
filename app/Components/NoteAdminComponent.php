<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::noteAdmin(); ?>
*/

class NoteAdminComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $admin;

        if (!$admin) {
            return "!delete!";
        } else {
            return "<b>nota</b> : ";
        }
    }
}
