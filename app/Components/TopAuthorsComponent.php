<?php

use App\Support\Sanitize;
use App\Library\Components\BaseComponent;

/*
<?= Component::TopAuthors(5); ?>
*/

class TopAuthorsComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $content = '';

        $arg = is_array($params) ? ($params[0] ?? 0) : $params;
        $arg = (int)$arg;

        $result = sql_query("SELECT aid, counter FROM " . sql_prefix('authors') . " ORDER BY counter DESC LIMIT 0,$arg");
        
        while (list($aid, $counter) = sql_fetch_row($result)) {
            if ($counter > 0) {
                $content .= '<li class="ms-4 my-1">
                    <a href="search.php?query=&amp;author=' . $aid . '" >' . $aid . '</a>&nbsp;
                    <span class="badge bg-secondary float-end">' . Sanitize::wrh($counter) . '</span></li>';
            }
        }

        sql_free_result($result);

        return $content;
    }
}
