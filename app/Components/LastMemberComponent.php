<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::lastMember(); ?>
    <?= Component::lastMember([]); ?>
*/

class LastMemberComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $query  = sql_query("SELECT uname FROM " . sql_prefix('users') . " ORDER BY uid DESC LIMIT 0,1");
        $result = sql_fetch_row($query);

        return $result[0] ?? '';
    }
}
