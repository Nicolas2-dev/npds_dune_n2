<?php

use App\Support\Facades\Groupe;
use App\Library\Components\BaseComponent;

/**
 * Composant NoGroupeText
 * [french]Forme de ELSE de groupe_text / Test si le membre n'appartient pas aux(x) groupe(s) et n'affiche que le texte encadré par no_groupe_textID(ID_group) ... !/!
 * Si no_groupe_ID est nul, la vérification portera sur qualité d'anonyme
 * Syntaxe : no_groupe_text(), no_groupe_text(10) ou no_groupe_textID("gp1,gp2,gp3") ... !/![/french]
 *
 * Exemple d'appel :
 *    <?= Component::noGroupeText(10); ?>
 *    <?= Component::noGroupeText("gp1,gp2"); ?>
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
