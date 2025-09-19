<?php

use App\Support\Sanitize;
use App\Library\Components\BaseComponent;

/*
<?= Component::TopStorieAuthors(5); ?>
*/

class TopStorieAuthorsComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $content = '';

        $arg = is_array($params) ? ($params[0] ?? 0) : $params;
        $arg = (int)$arg;

        $result = sql_query("SELECT uname, counter FROM " . sql_prefix('users') . " ORDER BY counter DESC LIMIT 0,$arg");
        
        while (list($uname, $counter) = sql_fetch_row($result)) {
            if ($counter > 0) {
                $content .= '<li class="ms-4 my-1"><a href="user.php?op=userinfo&amp;uname='.$uname.'" >'.$uname.'</a>
                    &nbsp;<span class="badge bg-secondary float-end">' . Sanitize::wrh($counter) . '</span></li>';
            }
        }

        sql_free_result($result);

        return $content;
    }
}
