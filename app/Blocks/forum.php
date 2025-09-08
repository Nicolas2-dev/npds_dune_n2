<?php

use App\Library\Theme\Theme;
use App\Library\Groupe\Groupe;
use App\Library\String\Sanitize;

if (! function_exists('RecentForumPosts')) {
    #autodoc RecentForumPosts($title, $maxforums, $maxtopics, $dposter, $topicmaxchars,$hr,$decoration) : Bloc Forums <br />=> syntaxe :<br />function#RecentForumPosts<br />params#titre, nb_max_forum (O=tous), nb_max_topic, affiche_l'emetteur(true / false), topic_nb_max_char, affiche_HR(true / false),
    function RecentForumPosts($title, $maxforums, $maxtopics, $displayposter = false, $topicmaxchars = 15, $hr = false, $decoration = '')
    {
        $boxstuff = RecentForumPosts_fab($title, $maxforums, $maxtopics, $displayposter, $topicmaxchars, $hr, $decoration);

        global $block_title;

        if ($title == '') {
            $title = $block_title == '' ? translate('Forums infos') : $block_title;
        }

        Theme::themeSidebox($title, $boxstuff);
    }
}

if (! function_exists('RecentForumPosts_fab')) {
    function RecentForumPosts_fab($title, $maxforums, $maxtopics, $displayposter, $topicmaxchars, $hr, $decoration)
    {
        global $parse, $user;

        $topics = 0;

        settype($maxforums, 'integer');
        settype($maxtopics, 'integer');

        $lim = $maxforums == 0 ? '' : ' LIMIT ' . $maxforums;

        $query = $user
            ? "SELECT * FROM " . sql_prefix('forums') . " 
            ORDER BY cat_id,forum_index, forum_id" . $lim

            : "SELECT * FROM " . sql_prefix('forums') . " 
            WHERE forum_type!='9' 
            AND forum_type!='7' 
            AND forum_type!='5' 
            ORDER BY cat_id, forum_index, forum_id" . $lim;

        $result = sql_query($query);

        if (!$result) {
            exit();
        }

        $boxstuff = '<ul>';

        while ($row = sql_fetch_row($result)) {
            if (($row[6] == '5') or ($row[6] == '7')) {
                $ok_affich = false;

                $tab_groupe = Groupe::validGroup($user);
                $ok_affich = Groupe::groupeForum($row[7], $tab_groupe);
            } else {
                $ok_affich = true;
            }

            if ($ok_affich) {
                $forumid = $row[0];
                $forumname = $row[1];
                $forum_desc = $row[2];

                if ($hr) {
                    $boxstuff .= '<li><hr /></li>';
                }

                if ($parse == 0) {
                    $forumname = Sanitize::fixQuotes($forumname);
                    $forum_desc = Sanitize::fixQuotes($forum_desc);
                } else {
                    $forumname = stripslashes($forumname);
                    $forum_desc = stripslashes($forum_desc);
                }

                $res = sql_query("SELECT * 
                                FROM " . sql_prefix('forumtopics') . " 
                                WHERE forum_id = '$forumid' 
                                ORDER BY topic_time DESC");

                $ibidx = sql_num_rows($res);

                $boxstuff .= '<li class="list-unstyled border-0 p-2 mt-1"><h6><a href="viewforum.php?forum=' . $forumid . '" title="' . strip_tags($forum_desc) . '" data-bs-toggle="tooltip">' . $forumname . '</a><span class="float-end badge bg-primary" title="' . translate('Sujets') . '" data-bs-toggle="tooltip">' . $ibidx . '</span></h6></li>';

                $topics = 0;

                while (($topics < $maxtopics) && ($topicrow = sql_fetch_row($res))) {

                    $topicid = $topicrow[0];
                    $tt = $topictitle = $topicrow[1];
                    // $date = $topicrow[3]; // ???

                    $replies = 0;

                    $postquery = "SELECT COUNT(*) AS total 
                                FROM " . sql_prefix('posts') . " 
                                WHERE topic_id = '$topicid'";

                    if ($pres = sql_query($postquery)) {
                        if ($myrow = sql_fetch_assoc($pres)) {
                            $replies = $myrow['total'];
                        }
                    }

                    if (strlen($topictitle) > $topicmaxchars) {
                        $topictitle = substr($topictitle, 0, $topicmaxchars);
                        $topictitle .= '..';
                    }

                    if ($displayposter) {
                        $posterid = $topicrow[2];
                        $RowQ1 = Q_Select("SELECT uname 
                                        FROM " . sql_prefix('users') . " 
                                        WHERE uid = '$posterid'", 3600);

                        $myrow = $RowQ1[0];
                        $postername = $myrow['uname'];
                    }

                    if ($parse == 0) {
                        $tt =  strip_tags(Sanitize::fixQuotes($tt));
                        $topictitle = Sanitize::fixQuotes($topictitle);
                    } else {
                        $tt =  strip_tags(stripslashes($tt));
                        $topictitle = stripslashes($topictitle);
                    }

                    $boxstuff .= '<li class="list-group-item p-1 border-right-0 border-left-0 list-group-item-action"><div class="n-ellipses"><span class="badge bg-secondary mx-2" title="' . translate('Réponses') . '" data-bs-toggle="tooltip" data-bs-placement="top">' . $replies . '</span><a href="viewtopic.php?topic=' . $topicid . '&amp;forum=' . $forumid . '" >' . $topictitle . '</a></div>';

                    if ($displayposter) {
                        $boxstuff .= $decoration . '<span class="ms-1">' . $postername . '</span>';
                    }

                    $boxstuff .= '</li>';

                    $topics++;
                }
            }
        }

        $boxstuff .= '</ul>';

        return $boxstuff;
    }
}
