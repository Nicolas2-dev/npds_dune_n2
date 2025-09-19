<?php

use App\Support\Sanitize;
use App\Support\Facades\Groupe;
use App\Library\Components\BaseComponent;

/* Exemple d'appel :
<?= Component::EspaceGroupe('gp1', 1, 1); ?>
*/
class EspaceGroupeComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        [$gr, $t_gr, $i_gr] = is_array($params) ? $params : [$params[0], $params[1], $params[2]];

        $gr   = Sanitize::argFilter($gr);
        $t_gr = Sanitize::argFilter($t_gr);
        $i_gr = Sanitize::argFilter($i_gr);

        return Groupe::fabEspaceGroupe($gr, $t_gr, $i_gr);
    }
}
