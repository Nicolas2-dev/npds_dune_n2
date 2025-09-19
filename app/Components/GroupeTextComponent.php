<?php

use App\Support\Facades\Groupe;
use App\Library\Components\BaseComponent;

/**
 * Composant GroupeText
 * [french]Test si le membre appartient aux(x) groupe(s) et n'affiche que le texte encadré par groupe_textID(ID_group) ... !/!
 * Si groupe_ID est nul, la vérification portera simplement sur la qualité de membre
 * Syntaxe : groupe_text(), groupe_text(10) ou groupe_textID("gp1,gp2,gp3") ... !/![/french]
 *
 * Exemple d'appel :
 *    <?= Component::groupeText(10); ?>
 *    <?= Component::groupeText("gp1,gp2"); ?>
 */
class GroupeTextComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $user;

        $arg = is_array($params) ? ($params[0] ?? '') : $params;
        $affich = false;
        $remp   = "";

        if ($arg != "") {
            if (Groupe::groupeAutorisation($arg, Groupe::validGroup($user))) {
                $affich = true;
            }
        } else {
            if ($user) {
                $affich = true;
            }
        }

        if (!$affich) {
            $remp = "!delete!";
        }

        return $remp;
    }
}
