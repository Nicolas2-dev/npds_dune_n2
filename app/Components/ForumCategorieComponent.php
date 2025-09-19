<?php

namespace App\Components;

use App\Support\Sanitize;
use App\Support\Facades\Forum;
use App\Library\Components\BaseComponent;

/*
Exemples d'appel :
    <?= Component::forumCategorie('1,3'); ?>
*/

class ForumCategorieComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $arg = is_array($params) ? ($params[0] ?? '') : $params;
        $arg = Sanitize::argFilter($arg);

        $bid_tab = explode(",", $arg);
        $sql = "";
        foreach ($bid_tab as $cat) {
            $sql .= "cat_id='$cat' OR ";
        }
        $sql = substr($sql, 0, -4);

        $rowQ1 = Q_Select("SELECT * FROM " . sql_prefix('catagories') . " WHERE $sql", 3600);
        
        return @Forum::forum($rowQ1);
    }
}
