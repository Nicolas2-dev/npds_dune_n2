<?php

namespace App\Components;

use App\Support\Sanitize;
use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::TopSections(5); ?>
*/

class TopSectionsComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $content = '';

        $arg = is_array($params) ? ($params[0] ?? 0) : $params;
        $arg = (int)$arg;

        $result = sql_query("SELECT artid, title, counter FROM " . sql_prefix('seccont') . " ORDER BY counter DESC LIMIT 0,$arg");

        while (list($artid, $title, $counter) = sql_fetch_row($result)) {
            $content .= '<li class="ms-4 my-1">
            <a href="sections.php?op=viewarticle&amp;artid=' . $artid . '" >' . Language::affLangue($title) . '</a>
            &nbsp;<span class="badge bg-secondary float-end">' . Sanitize::wrh($counter) . ' ' . translate("Fois") . '</span></li>';
        }

        sql_free_result($result);

        return $content;
    }
}
