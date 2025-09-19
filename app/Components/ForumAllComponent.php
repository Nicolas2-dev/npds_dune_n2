<?php

namespace App\Components;

use App\Support\Facades\Forum;
use App\Library\Components\BaseComponent;

/*
Exemples d'appel :
    <?= Component::forumAll(); ?>
*/

class ForumAllComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $rowQ1 = Q_Select("SELECT * FROM " . sql_prefix('catagories') . " ORDER BY cat_id", 3600);

        return @Forum::forum($rowQ1);
    }
}