<?php

use App\Library\Components\BaseComponent;

/* Exemple d'appel :
<?= Component::sqlNbReq(); ?>
*/
class SqlNbReqComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $sql_nbREQ;

        return "SQL REQ : $sql_nbREQ";
    }
}
