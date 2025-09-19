<?php

namespace App\Components;

use Npds\Config\Config;
use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;

/* Exemple d'appel :
    <?= Component::TopicSubscribe('col-6'); ?>
*/

class TopicSubscribeComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $user, $cookie;

        $segment = is_array($params) ? ($params[0] ?? '') : $params;
        $aff = '';

        if (Config::get('user.subscribe') && $user) {
            $aff = '<div class="mb-3 row">';
            $result = sql_query("SELECT topicid, topictext, topicname FROM " . sql_prefix('topics') . " ORDER BY topicname");

            while(list($topicid, $topictext, $topicname) = sql_fetch_row($result)) {
                $resultX = sql_query("SELECT topicid FROM " . sql_prefix('subscribe') . " WHERE uid='$cookie[0]' AND topicid='$topicid'");

                $checked = sql_num_rows($resultX) == 1 ? 'checked' : '';

                $aff .= '<div class="'.$segment.'">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="Subtopicid['.$topicid.']" id="subtopicid'.$topicid.'" '.$checked.' />
                                <label class="form-check-label" for="subtopicid'.$topicid.'">'.Language::affLangue($topicname).'</label>
                            </div>
                        </div>';
            }

            $aff .= '</div>';
            sql_free_result($result);
        }

        return $aff;
    }
}
