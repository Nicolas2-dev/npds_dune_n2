<?php

namespace App\Components;

use App\Support\Facades\Block;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::blocnote('R1'); ?>
    <?= Component::blocnote(['code' => 'R1']); ?>
    <?= Component::blocnote(['R1']); ?>
*/

class BlocnoteComponent extends BaseComponent
{
    /**
     * Rend le blocnote
     *
     * @param array|string $params ID du bloc ou ['code' => ID]
     * @return string HTML du blocnote
     */
    public function render(array|string $params = []): string
    {
        global $REQUEST_URI;

        // Récupération de l'ID
        if (is_string($params)) {
            $id = $params;
        } elseif (is_array($params)) {
            $id = $params['code'] ?? ($params[0] ?? '');
        } else {
            $id = '';
        }

        // Bloc vide si on est dans l'admin
        if (stristr($REQUEST_URI, "admin.php")) {
            return "";
        }

        return @Block::oneBlock($id, "RB");
    }
}
