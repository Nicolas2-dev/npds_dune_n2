<?php

namespace App\Http\Controllers\Front\News;

use App\Http\Controllers\Core\FrontBaseController;


class Article extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        if (!isset($sid) && !isset($tid)) {
            header('Location: index.php');
        }

        if (!isset($archive)) {
            $archive = 0;
        }

        $xtab = (!$archive)
            ? News::newsAff('libre', "WHERE sid='$sid'", 1, 1)
            : News::newsAff('archive', "WHERE sid='$sid'", 1, 1);

        list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[0];

        if (!$aid) {
            header('Location: index.php');
        }

        sql_query("UPDATE " . sql_prefix('stories') . " 
                SET counter=counter+1 
                WHERE sid='$sid'");

        include 'header.php';

        // Include cache manager
        global $SuperCache;
        if ($SuperCache) {
            $cache_obj = new SuperCacheManager();
            $cache_obj->startCachingPage();
        } else {
            $cache_obj = new SuperCacheEmpty();
        }

        if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {

            $title      = Language::affLangue(stripslashes($title));
            $hometext   = Code::affCode(Language::affLangue(stripslashes($hometext)));
            $bodytext   = Code::affCode(Language::affLangue(stripslashes($bodytext)));
            $notes      = Code::affCode(Language::affLangue(stripslashes($notes)));

            if ($notes != '') {
                $notes = '<div class="note blockquote">' . translate('Note') . ' : ' . $notes . '</div>';
            }

            $bodytext = $bodytext == ''
                ? Metalang::metaLang($hometext . '<br />' . $notes)
                : Metalang::metaLang($hometext . '<br />' . $bodytext . '<br />' . $notes);

            if ($informant == '') {
                $informant = $anonymous;
            }

            News::getTopics($sid);

            if ($catid != 0) {
                $resultx = sql_query("SELECT title 
                                    FROM " . sql_prefix('stories_cat') . " 
                                    WHERE catid='$catid'");

                list($title1) = sql_fetch_row($resultx);

                $title = '<a href="index.php?op=newindex&amp;catid=' . $catid . '"><span>' . Language::affLangue($title1) . '</span></a> : ' . $title;
            }

            $boxtitle = translate('Liens relatifs');

            $boxstuff = '<ul>';

            $result = sql_query("SELECT name, url 
                                FROM " . sql_prefix('related') . " 
                                WHERE tid='$topic'");

            while (list($name, $url) = sql_fetch_row($result)) {
                $boxstuff .= '<li><a href="' . $url . '" target="_blank"><span>' . $name . '</span></a></li>';
            }

            $boxstuff .= '</ul>
            <ul>
                <li>
                    <a href="search.php?topic=' . $topic . '" >
                        ' . translate('En savoir plus à propos de') . ' : 
                    </a>
                    <span class="h5">
                        <span class="badge bg-secondary" title="' . $topicname . '<hr />' . Language::affLangue($topictext) . '" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right">
                            ' . Language::affLangue($topicname) . '
                        </span>
                    </span>
                </li>
                <li>
                    <a href="search.php?member=' . $informant . '" >
                        ' . translate('Article de') . ' ' . $informant . '
                    </a> ' . userpopover($informant, 36, '') . '
                </li>
            </ul>
            <div>
                <span class="fw-semibold">
                    ' . translate('L\'article le plus lu à propos de') . ' : 
                </span>
                <span class="h5">
                    <span class="badge bg-secondary" title="' . $topicname . '<hr />' . Language::affLangue($topictext) . '" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right">
                        ' . Language::affLangue($topicname) . '
                    </span>
                </span>
            </div>';

            $xtab = News::newsAff("big_story", "WHERE topic=$topic", 1, 1);

            list($topstory, $ttitle) = $xtab[0];

            $boxstuff .= '<ul>
                <li>
                    <a href="article.php?sid=' . $topstory . '" >
                        ' . Language::affLangue($ttitle) . '
                    </a>
                </li>
            </ul>
            <div>
                <span class="fw-semibold">
                    ' . translate('Les dernières nouvelles à propos de') . ' : 
                </span>
                <span class="h5">
                    <span class="badge bg-secondary" title="' . $topicname . '<hr />' . Language::affLangue($topictext) . '" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right">
                        ' . Language::affLangue($topicname) . '
                    </span>
                </span>
            </div>';

            $xtab = (!$archive)
                ? News::newsAff('libre', "WHERE topic=$topic AND archive='0' ORDER BY sid DESC LIMIT 0,5", 0, 5)
                : News::newsAff('archive', "WHERE topic=$topic AND archive='1' ORDER BY sid DESC LIMIT 0,5", 0, 5);

            $story_limit = 0;

            $boxstuff .= '<ul>';

            while (($story_limit < 5) and ($story_limit < sizeof($xtab))) {

                list($sid1, $catid1, $aid1, $title1) = $xtab[$story_limit];
                $story_limit++;

                $title1 = Language::affLangue(addslashes($title1));

                $boxstuff .= '<li>
                    <a href="article.php?sid=' . $sid1 . '&amp;archive=' . $archive . '" >
                        ' . Language::affLangue(stripslashes($title1)) . '
                    </a>
                </li>';
            }

            $boxstuff .= '</ul>
            <p align="center">
                <a href="print.php?sid=' . $sid . '" >
                    <i class="fa fa-print fa-2x me-3" title="' . translate('Page spéciale pour impression') . '" data-bs-toggle="tooltip"></i>
                </a>
                <a href="friend.php?op=FriendSend&amp;sid=' . $sid . '&amp;archive=' . $archive . '">
                    <i class="fa fa-2x fa-at" title="' . translate('Envoyer cet article à un ami') . '" data-bs-toggle="tooltip"></i>
                </a>
            </p>';

            if (!$archive) {
                $previous_tab = News::newsAff('libre', "WHERE sid<'$sid' ORDER BY sid DESC ", 0, 1);
                $next_tab = News::newsAff('libre', "WHERE sid>'$sid' ORDER BY sid ASC ", 0, 1);
            } else {
                $previous_tab = News::newsAff('archive', "WHERE sid<'$sid' ORDER BY sid DESC", 0, 1);
                $next_tab = News::newsAff('archive', "WHERE sid>'$sid' ORDER BY sid ASC ", 0, 1);
            }

            if (array_key_exists(0, $previous_tab)) {
                list($previous_sid) = $previous_tab[0];
            } else {
                $previous_sid = 0;
            }

            if (array_key_exists(0, $next_tab)) {
                list($next_sid) = $next_tab[0];
            } else {
                $next_sid = 0;
            }

            themearticle($aid, $informant, $time, $title, $bodytext, $topic, $topicname, $topicimage, $topictext, $sid, $previous_sid, $next_sid, $archive);

            // theme sans le système de commentaire en meta-mot !
            if (!function_exists('Caff_pub')) {
                if (file_exists('modules/comments/config/article.php')) {
                    include 'modules/comments/config/article.php';
                    include 'modules/comments/http/controllers/front/comments.php';
                }
            }
        }

        if ($SuperCache) {
            $cache_obj->endCachingPage();
        }

        include 'footer.php';
    }

}
