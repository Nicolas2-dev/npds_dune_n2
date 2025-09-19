<?php

namespace App\Components;

use App\Support\Sanitize;
use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::TopCategories(5); ?>
*/

class TopCategoriesComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $content = '';

        $arg = is_array($params) ? ($params[0] ?? 0) : $params;
        $arg = (int)$arg;

        $result = sql_query("SELECT catid, title, counter FROM " . sql_prefix('stories_cat') . " ORDER BY counter DESC LIMIT 0,$arg");

        while (list($catid, $title, $counter) = sql_fetch_row($result)) {
            if ($counter > 0) {
                $content .= '<li class="ms-4 my-1">
                <a href="index.php?op=newindex&amp;catid=' . $catid . '" >' . Language::affLangue($title) . '</a>
                &nbsp;<span class="badge bg-secondary float-end">' . Sanitize::wrh($counter) . '</span></li>';
            }
        }

        sql_free_result($result);

        return $content;
    }
}
