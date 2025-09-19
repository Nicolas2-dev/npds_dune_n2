<?php

use App\Support\Facades\Edito;
use App\Library\Components\BaseComponent;

/**
 * Composant Edito
 * [french]Fabrique et affiche l'EDITO[/french]
 *
 * Exemple d'appel :
 *    <?= Component::edito(); ?>
 *    <?= Component::edito([]); ?>
 */
class EditoComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        list($affich, $M_edito) = Edito::fabEdito();

        if ((!$affich) || ($M_edito == "")) {
            $M_edito = "";
        }

        return $M_edito;
    }
}
