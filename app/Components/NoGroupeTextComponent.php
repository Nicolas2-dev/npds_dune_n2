<?php

namespace App\Components;

use App\Support\Facades\Groupe;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::noGroupeText(10); ?>
    <?= Component::noGroupeText("gp1,gp2"); ?>
*/

class NoGroupeTextComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $user;

        $arg = is_array($params) ? ($params[0] ?? '') : $params;
        $affich = true;
        $remp   = "";

        if ($arg != "") {
            if (Groupe::groupeAutorisation($arg, Groupe::validGroup($user))) {
                $affich = false;
            }
            if (!$user) {
                $affich = false;
            }
        } else {
            if ($user) {
                $affich = false;
            }
        }

        if (!$affich) {
            $remp = "!delete!";
        }

        return $remp;
    }
}
