<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::listMns(); ?>
    <?= Component::listMns([]); ?>
*/

class ListMnsComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $query = sql_query("SELECT uname FROM " . sql_prefix('users') . " WHERE mns='1'");
        $html  = "<ul class=\"list-group list-group-flush\">";
        
        while (list($uname) = sql_fetch_row($query)) {
            $html .= "<li class=\"list-group-item\">
                        <a href=\"minisite.php?op=$uname\" target=\"_blank\">$uname</a>
                      </li>";
        }

        $html .= "</ul>";

        return $html;
    }
}
