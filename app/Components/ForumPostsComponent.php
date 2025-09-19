<?php

namespace App\Components;

use App\Support\Facades\Theme;
use App\Support\Facades\Groupe;
use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::forumPosts(); ?>
    <?= Component::forumPosts(['max' => 10, 'hot_threshold' => 5]); ?>
*/

class ForumPostsComponent extends BaseComponent
{
    /**
     * Rend les derniers posts du forum
     *
     * @param array $params Paramètres optionnels :
     *                      - 'max' => nombre maximum de posts (défaut 15)
     *                      - 'hot_threshold' => nombre de réponses pour sujet chaud (défaut 10)
     * @return string HTML du tableau des posts
     */
    public function render(array $params = []): string
    {
        global $cookie, $user;

        $hot_threshold = $params['hot_threshold'] ?? 10;
        $maxcount = $params['max'] ?? 15;

        $html = '<table cellspacing="3" cellpadding="3" width="top" border="0">'
            . '<tr align="center" class="ligna">'
            . '<th width="5%">' .  Language::affLangue('[french]Etat[/french][english]State[/english]') . '</th>'
            . '<th width="20%">' . Language::affLangue('[french]Forum[/french][english]Forum[/english]') . '</th>'
            . '<th width="30%">' . Language::affLangue('[french]Sujet[/french][english]Topic[/english]') . '</th>'
            . '<th width="5%">' .  Language::affLangue('[french]Réponse[/french][english]Reply[/english]') . '</th>'
            . '<th width="20%">' . Language::affLangue('[french]Dernier Auteur[/french][english]Last author[/english]') . '</th>'
            . '<th width="20%">' . Language::affLangue('[french]Date[/french][english]Date[/english]') . '</th>'
            . '</tr>';

        $result = sql_query("SELECT MAX(post_id) 
                             FROM " . sql_prefix('posts') . " 
                             WHERE forum_id > 0 
                             GROUP BY topic_id 
                             ORDER BY MAX(post_id) DESC 
                             LIMIT 0, $maxcount");

        while (list($post_id) = sql_fetch_row($result)) {
            $res = sql_query("SELECT 
                    us.topic_id, us.forum_id, us.poster_id, us.post_time, 
                    uv.topic_title, 
                    ug.forum_name, ug.forum_type, ug.forum_pass, 
                    ut.uname 
                FROM 
                    " . sql_prefix('posts') . " us, 
                    " . sql_prefix('forumtopics') . " uv, 
                    " . sql_prefix('forums') . " ug, 
                    " . sql_prefix('users') . " ut 
                WHERE 
                    us.post_id = $post_id 
                    AND uv.topic_id = us.topic_id 
                    AND uv.forum_id = ug.forum_id 
                    AND ut.uid = us.poster_id LIMIT 1");
            list($topic_id, $forum_id, $poster_id, $post_time, $topic_title, $forum_name, $forum_type, $forum_pass, $uname) = sql_fetch_row($res);

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
                . '<td><a href="user.php?op=userinfo&uname=' . $uname . '">' . $uname . '</a></td>'
                . '<td align="center">' . $post_time . '</td>'
                . '</tr>';
        }

        $html .= '</table>';

        return $html;
    }
}
