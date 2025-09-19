<?php

use App\Support\Facades\Theme;
use App\Support\Facades\Groupe;
use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;

/*
<?= Component::forumLastPosts(); ?>
<?= Component::forumLastPosts(['max' => 5, 'hot_threshold' => 8]); ?>
*/

/**
 * Composant ForumLastPosts
 *
 * [french]Retourne les derniers posts des forums en tenant compte des groupes.[/french]
 * [english]Returns the latest forum posts respecting user groups.[/english]
 */
class ForumLastPostsComponent extends BaseComponent
{
    /**
     * Rend les derniers posts du forum (tableau simplifié)
     *
     * @param array $params Paramètres optionnels :
     *                      - 'max' => nombre maximum de posts (défaut 10)
     *                      - 'hot_threshold' => nombre de réponses pour sujet chaud (défaut 10)
     * @return string HTML du tableau
     */
    public function render(array $params = []): string
    {
        global $cookie, $user;

        $hot_threshold = $params['hot_threshold'] ?? 10;
        $maxcount = $params['max'] ?? 10;

        $html = '<table cellspacing="3" cellpadding="3" width="top" border="0">'
            . '<tr align="center" class="ligna">'
            . '<td width="8%">' .  Language::affLangue('[french]Etat[/french][english]State[/english]') . '</td>'
            . '<td width="35%">' . Language::affLangue('[french]Forum[/french][english]Forum[/english]') . '</td>'
            . '<td width="50%">' . Language::affLangue('[french]Sujet[/french][english]Topic[/english]') . '</td>'
            . '<td width="7%">' .  Language::affLangue('[french]Réponses[/french][english]Replies[/english]') . '</td>'
            . '</tr>';

        $result = sql_query("SELECT MAX(post_id) 
                             FROM " . sql_prefix('posts') . " 
                             WHERE forum_id > 0 
                             GROUP BY topic_id 
                             ORDER BY MAX(post_id) DESC 
                             LIMIT 0,$maxcount");

        while (list($post_id) = sql_fetch_row($result)) {
            $res = sql_query("SELECT 
                    us.topic_id, us.forum_id, us.poster_id, 
                    uv.topic_title, 
                    ug.forum_name, ug.forum_type, ug.forum_pass 
                FROM 
                    " . sql_prefix('posts') . " us, 
                    " . sql_prefix('forumtopics') . " uv, 
                    " . sql_prefix('forums') . " ug 
                WHERE 
                    us.post_id = $post_id 
                    AND uv.topic_id = us.topic_id 
                    AND uv.forum_id = ug.forum_id LIMIT 1");

            list($topic_id, $forum_id, $poster_id, $topic_title, $forum_name, $forum_type, $forum_pass) = sql_fetch_row($res);

            $ok_affich = true;
            if (in_array($forum_type, ['5','7'])) {
                $tab_groupe = Groupe::validGroup($user);
                $ok_affich  = Groupe::groupeForum($forum_pass, $tab_groupe);
            }

            if (!$ok_affich) continue;

            $TableRep = sql_query("SELECT * FROM " . sql_prefix('posts') . " WHERE forum_id > 0 AND topic_id = '$topic_id'");
            $replys = sql_num_rows($TableRep) - 1;

            $sqlR = "SELECT rid FROM " . sql_prefix('forum_read') . " WHERE topicid = '$topic_id' AND uid = '{$cookie[0]}' AND status != '0'";

            $imgtmpHR = Theme::themeImage("forum/icons/hot_red_folder.gif") ?: "images/forum/icons/hot_red_folder.gif";
            $imgtmpH  = Theme::themeImage("forum/icons/hot_folder.gif") ?: "images/forum/icons/hot_folder.gif";
            $imgtmpR  = Theme::themeImage("forum/icons/red_folder.gif") ?: "images/forum/icons/red_folder.gif";
            $imgtmpF  = Theme::themeImage("forum/icons/folder.gif") ?: "images/forum/icons/folder.gif";
            $imgtmpL  = Theme::themeImage("forum/icons/lock.gif") ?: "images/forum/icons/lock.gif";

            if ($replys >= $hot_threshold) {
                $image = sql_num_rows(sql_query($sqlR)) == 0 ? $imgtmpHR : $imgtmpH;
            } else {
                $image = sql_num_rows(sql_query($sqlR)) == 0 ? $imgtmpR : $imgtmpF;
            }

            // Si topic verrouillé
            if ($myrow['topic_status'] ?? 0 != 0) $image = $imgtmpL;

            $html .= '<tr class="lignb">'
                . '<td align="center"><img src="' . $image . '"></td>'
                . '<td><a href="viewforum.php?forum=' . $forum_id . '">' . $forum_name . '</a></td>'
                . '<td><a href="viewtopic.php?topic=' . $topic_id . '&forum=' . $forum_id . '">' . $topic_title . '</a></td>'
                . '<td align="center">' . $replys . '</td>'
                . '</tr>';
        }

        $html .= '</table>';

        return $html;
    }
}
