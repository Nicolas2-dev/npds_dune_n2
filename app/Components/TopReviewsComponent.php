<?php

namespace App\Components;

use App\Support\Sanitize;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::TopReviews(5); ?>
*/

class TopReviewsComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $content = '';

        $arg = is_array($params) ? ($params[0] ?? 0) : $params;
        $arg = (int)$arg;

        $result = sql_query("SELECT id, title, hits FROM " . sql_prefix('reviews') . " ORDER BY hits DESC LIMIT 0,$arg");

        while (list($id, $title, $hits) = sql_fetch_row($result)) {
            if ($hits > 0) {
                $content .= '<li class="ms-4 my-1">
                    <a href="reviews.php?op=showcontent&amp;id=' . $id . '" >' . $title . '</a>&nbsp;
                    <span class="badge bg-secondary float-end">' . Sanitize::wrh($hits) . ' ' . translate("Fois") . '</span></li>';
            }
        }
        
        sql_free_result($result);

        return $content;
    }
}
