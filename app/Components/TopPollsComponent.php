<?php

use App\Support\Sanitize;
use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;

/*
<?= Component::TopPolls(5); ?>
*/

class TopPollsComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $content = '';

        $arg = is_array($params) ? ($params[0] ?? 0) : $params;

        $arg = (int)$arg;

        $result = sql_query("SELECT pollID, pollTitle, voters FROM " . sql_prefix('poll_desc') . " ORDER BY voters DESC LIMIT 0,$arg");
        
        while (list($pollID, $pollTitle, $voters) = sql_fetch_row($result)) {
            if ($voters > 0) {
                $content .= '<li class="ms-4 my-1">
                    <a href="pollBooth.php?op=results&amp;pollID=' . $pollID . '" >' . Language::affLangue($pollTitle) . '</a>
                    &nbsp;<span class="badge bg-secondary float-end">' . Sanitize::wrh($voters) . '</span></li>';
            }
        }

        sql_free_result($result);

        return $content;
    }
}
