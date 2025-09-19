<?php

use Npds\Config\Config;
use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;

/*
<?= Component::TopicAll(); ?>
*/

class TopicAllComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $tipath = Config::get('graphics.tipath');

        $aff = '<div class="">';

        $result = sql_query("SELECT topicid, topicname, topicimage, topictext FROM " . sql_prefix('topics') . " ORDER BY topicname");

        while (list($topicid, $topicname, $topicimage, $topictext) = sql_fetch_row($result)) {

            $resultn = sql_query("SELECT COUNT(*) AS total FROM " . sql_prefix('stories') . " WHERE topic='$topicid'");
            $total_news = sql_fetch_assoc($resultn);

            $aff .= '<div class="col-sm-6 col-lg-4 mb-2 griditem px-2">
                        <div class="card my-2">';

            if (($topicimage) && file_exists("$tipath$topicimage")) {
                $aff .= '<img class="mt-3 ms-3 n-sujetsize" src="' . $tipath . $topicimage . '" alt="topic_icon" />';
            }

            $aff .= '<div class="card-body">';

            if ($total_news['total'] != '0') {
                $aff .= '<a href="index.php?op=newtopic&amp;topic=' . $topicid . '"><h4 class="card-title">' . Language::affLangue($topicname) . '</h4></a>';
            } else {
                $aff .= '<h4 class="card-title">' . Language::affLangue($topicname) . '</h4>';
            }

            $aff .= '<p class="card-text">' . Language::affLangue($topictext) . '</p>
                     <p class="card-text text-end"><span class="small">' . translate("Nb. d\'articles") . '</span> <span class="badge bg-secondary">' . $total_news['total'] . '</span></p>
                     </div>
                    </div>
                   </div>';
        }

        $aff .= '</div>';

        sql_free_result($result);

        return $aff;
    }
}
