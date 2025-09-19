<?php

namespace App\Components;

use App\Support\Sanitize;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::adminInfos('adminName'); ?>
*/

class AdminInfosComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $arg = is_array($params) ? ($params[0] ?? '') : $params;
        $arg = Sanitize::argFilter($arg);

        $rowQ1 = Q_select("SELECT url, email FROM " . sql_prefix('authors') . " WHERE aid='$arg'", 86400);
        $myrow = $rowQ1[0] ?? null;

        if (!$myrow) {
            return $arg;
        }

        if (!empty($myrow['url'])) {
            return '<a href="' . $myrow['url'] . '">' . $arg . '</a>';
        } elseif (!empty($myrow['email'])) {
            return '<a href="mailto:' . $myrow['email'] . '">' . $arg . '</a>';
        } else {
            return $arg;
        }
    }
}
