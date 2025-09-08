<?php

namespace App\Library\News;

use Npds\Config\Config;
use App\Library\Log\Log;
use App\Library\Code\Code;
use App\Library\Edito\Edito;
use App\Library\Theme\Theme;
use App\Library\Groupe\Groupe;
use App\Library\String\Sanitize;
use App\Library\Language\Language;
use App\Library\Metalang\Metalang;
use App\Library\Subscribe\Subscribe;


class News
{

    /**
     * Contrôle l'affichage d'un article ou d'une news selon le paramètre ihome et le catid.
     *
     * @param int $ihome Paramètre de visibilité (-1, 0, 1, 2...127, -127)
     * @param int $catid Identifiant de la catégorie (par défaut 0)
     * @return bool Retourne true si l'article doit être affiché, false sinon
     */
    public static function ctrlAff(int $ihome, int $catid = 0): bool
    {
        global $user; // global a revoir !

        $affich = false;

        if ($ihome == -1 and (!$user)) {
            $affich = true;
        } elseif ($ihome == 0) {
            $affich = true;
        } elseif ($ihome == 1) {
            $affich = $catid > 0 ? false : true;
        } elseif (($ihome > 1) and ($ihome <= 127)) {
            $tab_groupe = Groupe::validGroup($user);

            if ($tab_groupe) {
                foreach ($tab_groupe as $groupevalue) {
                    if ($groupevalue == $ihome) {
                        $affich = true;
                        break;
                    }
                }
            }
        } else {
            if ($user) {
                $affich = true;
            }
        }

        return $affich;
    }

    public static function automatedNews()
    {
        $gmt = Config::get('date.gmt'); // gmt a revoir !

        $today = getdate(time() + ((int)$gmt * 3600));
        $day = $today['mday'];

        if ($day < 10) {
            $day = '0' . $day;
        }

        $month = $today['mon'];

        if ($month < 10) {
            $month = '0' . $month;
        }

        $year = $today['year'];
        $hour = $today['hours'];
        $min = $today['minutes'];

        $result = sql_query("SELECT anid, date_debval 
                            FROM " . sql_prefix('autonews') . " 
                            WHERE date_debval LIKE '$year-$month%'");

        while (list($anid, $date_debval) = sql_fetch_row($result)) {
            preg_match('#^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$#', $date_debval, $date);

            if (($date[1] <= $year) and ($date[2] <= $month) and ($date[3] <= $day)) {
                if (($date[4] < $hour) and ($date[5] >= $min) or ($date[4] <= $hour) and ($date[5] <= $min) or (($day - $date[3]) >= 1)) {

                    $result2 = sql_query("SELECT catid, aid, title, hometext, bodytext, topic, informant, notes, ihome, date_finval, auto_epur 
                                        FROM " . sql_prefix('autonews') . " 
                                        WHERE anid='$anid'");

                    while (list($catid, $aid, $title, $hometext, $bodytext, $topic, $author, $notes, $ihome, $date_finval, $epur) = sql_fetch_row($result2)) {

                        $subject    = stripslashes(Sanitize::fixQuotes($title));
                        $hometext   = stripslashes(Sanitize::fixQuotes($hometext));
                        $bodytext   = stripslashes(Sanitize::fixQuotes($bodytext));
                        $notes      = stripslashes(Sanitize::fixQuotes($notes));

                        sql_query("INSERT INTO " . sql_prefix('stories') . " 
                                VALUES (NULL, '$catid', '$aid', '$subject', now(), '$hometext', '$bodytext', '0', '0', '$topic', '$author', '$notes', '$ihome', '0', '$date_finval', '$epur')");

                        sql_query("DELETE FROM " . sql_prefix('autonews') . " 
                                WHERE anid='$anid'");

                        if (Config::get('user.subscribe')) {
                            Subscribe::subscribeMail('topic', $topic, '', $subject, '');
                        }

                        // Réseaux sociaux
                        if (file_exists('modules/npds_twi/http/controllers/npds_to_twi.php')) {
                            include 'modules/npds_twi/http/controllers/npds_to_twi.php';
                        }

                        // module non fini fbk nexiste pas !
                        //if (file_exists('modules/npds_fbk/http/controllers/npds_to_fbk.php')) {
                        //    include 'modules/npds_twi/http/controllers/npds_to_fbk.php';
                        //}
                    }
                }
            }
        }

        // Purge automatique
        $result = sql_query("SELECT sid, date_finval, auto_epur 
                            FROM " . sql_prefix('stories') . " 
                            WHERE date_finval LIKE '$year-$month%'");

        while (list($sid, $date_finval, $epur) = sql_fetch_row($result)) {
            preg_match('#^(\d{4})-(\d{1,2})-(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$#', $date_finval, $date);

            if (($date[1] <= $year) and ($date[2] <= $month) and ($date[3] <= $day)) {
                if (($date[4] < $hour) and ($date[5] >= $min) or ($date[4] <= $hour) and ($date[5] <= $min)) {
                    if ($epur == 1) {

                        sql_query("DELETE FROM " . sql_prefix('stories') . " 
                                WHERE sid='$sid'");

                        if (file_exists('modules/comments/config/article.php')) {
                            include 'modules/comments/config/article.php';

                            sql_query("DELETE FROM " . sql_prefix('posts') . " 
                                    WHERE forum_id='$forum' 
                                    AND topic_id='$topic'");
                        }

                        Log::ecrireLog('security', sprintf('removeStory(%s, epur) by automated epur : system', $sid), '');
                    } else
                        sql_query("UPDATE " . sql_prefix('stories') . " 
                                SET archive='1' 
                                WHERE sid='$sid'");
                }
            }
        }
    }

    public static function affNews($op, $catid, $marqeur)
    {
        $url = $op;

        if ($op == 'edito-newindex') {
            if ($marqeur == 0) {
                Edito::affEdito();
            }

            $op = 'news';
        }

        if ($op == 'newindex') {
            $op = $catid == '' ? 'news' : 'categories';
        }

        if ($op == 'newtopic') {
            $op = 'topics';
        }

        if ($op == 'newcategory') {
            $op = 'categories';
        }

        $news_tab = News::prepaAffNews($op, $catid, $marqeur);
        $story_limit = 0;

        // si le tableau $news_tab est vide alors return 
        if (is_null($news_tab)) {
            return;
        }

        $newscount = sizeof($news_tab);

        while ($story_limit < $newscount) {
            $story_limit++;

            $aid = unserialize($news_tab[$story_limit]['aid']);
            $informant = unserialize($news_tab[$story_limit]['informant']);
            $datetime = unserialize($news_tab[$story_limit]['datetime']);
            $title = unserialize($news_tab[$story_limit]['title']);
            $counter = unserialize($news_tab[$story_limit]['counter']);
            $topic = unserialize($news_tab[$story_limit]['topic']);
            $hometext = unserialize($news_tab[$story_limit]['hometext']);
            $notes = unserialize($news_tab[$story_limit]['notes']);
            $morelink = unserialize($news_tab[$story_limit]['morelink']);
            $topicname = unserialize($news_tab[$story_limit]['topicname']);
            $topicimage = unserialize($news_tab[$story_limit]['topicimage']);
            $topictext = unserialize($news_tab[$story_limit]['topictext']);
            $s_id = unserialize($news_tab[$story_limit]['id']);

            Theme::themeIndex($aid, $informant, $datetime, $title, $counter, $topic, $hometext, $notes, $morelink, $topicname, $topicimage, $topictext, $s_id);
        }

        $transl1 = translate('Page suivante');
        $transl2 = translate('Home');

        global $cookie; // global a revoir !
        $storynum = isset($cookie[3]) ? $cookie[3] : Config::get('storie.storyhome');

        if ($op == 'categories') {
            if (sizeof($news_tab) == $storynum) {
                $marqeur = $marqeur + sizeof($news_tab);

                echo '<div class="text-end">
                    <a href="index.php?op=' . $url . '&amp;catid=' . $catid . '&amp;marqeur=' . $marqeur . '" class="page_suivante" >
                        ' . $transl1 . '<i class="fa fa-chevron-right fa-lg ms-2" title="' . $transl1 . '" data-bs-toggle="tooltip"></i>
                    </a>
                </div>';
            } else {
                if ($marqeur >= $storynum) {
                    echo '<div class="text-end">
                        <a href="index.php?op=' . $url . '&amp;catid=' . $catid . '&amp;marqeur=0" class="page_suivante" title="' . $transl2 . '">
                            ' . $transl2 . '
                        </a>
                    </div>';
                }
            }
        }

        if ($op == 'news') {
            if (sizeof($news_tab) == $storynum) {
                $marqeur = $marqeur + sizeof($news_tab);

                echo '<div class="text-end">
                    <a href="index.php?op=' . $url . '&amp;catid=' . $catid . '&amp;marqeur=' . $marqeur . '" class="page_suivante" >
                        ' . $transl1 . '<i class="fa fa-chevron-right fa-lg ms-2" title="' . $transl1 . '" data-bs-toggle="tooltip"></i>
                    </a>
                </div>';
            } else {
                if ($marqeur >= $storynum) {
                    echo '<div class="text-end">
                        <a href="index.php?op=' . $url . '&amp;catid=' . $catid . '&amp;marqeur=0" class="page_suivante" title="' . $transl2 . '">
                            ' . $transl2 . '
                        </a>
                    </div>';
                }
            }
        }

        if ($op == 'topics') {
            if (sizeof($news_tab) == $storynum) {
                $marqeur = $marqeur + sizeof($news_tab);

                echo '<div align="right">
                    <a href="index.php?op=newtopic&amp;topic=' . $topic . '&amp;marqeur=' . $marqeur . '" class="page_suivante" >
                        ' . $transl1 . '<i class="fa fa-chevron-right fa-lg ms-2" title="' . $transl1 . '" data-bs-toggle="tooltip"></i>
                    </a>
                </div>';
            } else {
                if ($marqeur >= $storynum) {
                    echo '<div class="text-end">
                        <a href="index.php?op=newtopic&amp;topic=' . $topic . '&amp;marqeur=0" class="page_suivante" title="' . $transl2 . '">
                            ' . $transl2 . '
                        </a>
                    </div>';
                }
            }
        }
    }

    /**
     * Récupère les news selon le type de requête et les critères de sélection.
     *
     * @param string $type_req Type de sélection ('index', 'old_news', 'big_story', 'big_topic', 'libre', 'archive')
     * @param string $sel Clause SQL WHERE ou similaire pour filtrer les news
     * @param int $storynum Nombre d'articles à récupérer
     * @param int|string $oldnum Ancien nombre d'articles (utilisé pour certains calculs)
     * @return array Tableau contenant les news récupérées
     */
    public static function newsAff(string $type_req, string $sel, int|string $storynum, int|string $oldnum): array
    {
        // pas stabilisé ...!
        // Astuce pour afficher le nb de News correct même si certaines News ne sont pas visibles (membres, groupe de membres)
        // En fait on * le Nb de News par le Nb de groupes
        $row_Q2 = Q_select("SELECT COUNT(groupe_id) AS total 
                            FROM " . sql_prefix('groupes'), 86400);

        $NumG = $row_Q2[0];

        if ($NumG['total'] < 2) {
            $coef = 2;
        } else {
            $coef = $NumG['total'];
        }

        settype($storynum, 'integer');

        if ($type_req == 'index') {
            $Xstorynum = $storynum * $coef;

            $result = Q_select("SELECT sid, catid, ihome 
                                FROM " . sql_prefix('stories') . " $sel 
                                ORDER BY sid DESC 
                                LIMIT $Xstorynum", 3600);
            $Znum = $storynum;
        }

        if ($type_req == 'old_news') {
            // $Xstorynum=$oldnum*$coef;

            $result = Q_select("SELECT sid, catid, ihome, time 
                                FROM " . sql_prefix('stories') . " $sel 
                                ORDER BY time DESC 
                                LIMIT $storynum", 3600);
            $Znum = $oldnum;
        }

        if (($type_req == 'big_story') or ($type_req == 'big_topic')) {
            // $Xstorynum=$oldnum*$coef;

            $result = Q_select("SELECT sid, catid, ihome, counter 
                                FROM " . sql_prefix('stories') . " $sel 
                                ORDER BY counter DESC 
                                LIMIT $storynum", 0);
            $Znum = $oldnum;
        }

        if ($type_req == 'libre') {
            $Xstorynum = $oldnum * $coef; //need for what ?

            $result = Q_select("SELECT sid, catid, ihome, time 
                                FROM " . sql_prefix('stories') . " $sel", 3600);
            $Znum = $oldnum;
        }

        if ($type_req == 'archive') {
            $Xstorynum = $oldnum * $coef; //need for what ?
            $result = Q_select("SELECT sid, catid, ihome 
                                FROM " . sql_prefix('stories') . " $sel", 3600);
            $Znum = $oldnum;
        }

        $ibid = 0;

        settype($tab, 'array');

        foreach ($result as $myrow) {

            $s_sid = $myrow['sid'];
            $catid = $myrow['catid'];
            $ihome = $myrow['ihome'];

            if (array_key_exists('time', $myrow)) {
                $time = $myrow['time'];
            }

            if ($ibid == $Znum) {
                break;
            }

            if ($type_req == 'libre') {
                $catid = 0;
            }

            if ($type_req == 'archive') {
                $ihome = 0;
            }

            if (static::ctrlAff($ihome, $catid)) {

                if (($type_req == 'index') or ($type_req == 'libre')) {
                    $result2 = sql_query("SELECT sid, catid, aid, title, time, hometext, bodytext, comments, counter, topic, informant, notes 
                                        FROM " . sql_prefix('stories') . " 
                                        WHERE sid='$s_sid' 
                                        AND archive='0'");
                }

                if ($type_req == 'archive') {
                    $result2 = sql_query("SELECT sid, catid, aid, title, time, hometext, bodytext, comments, counter, topic, informant, notes 
                                        FROM " . sql_prefix('stories') . " 
                                        WHERE sid='$s_sid' 
                                        AND archive='1'");
                }

                if ($type_req == 'old_news') {
                    $result2 = sql_query("SELECT sid, title, time, comments, counter 
                                        FROM " . sql_prefix('stories') . " 
                                        WHERE sid='$s_sid' 
                                        AND archive='0'");
                }

                if (($type_req == 'big_story') or ($type_req == 'big_topic')) {
                    $result2 = sql_query("SELECT sid, title 
                                        FROM " . sql_prefix('stories') . " 
                                        WHERE sid='$s_sid' 
                                        AND archive='0'");
                }

                $tab[$ibid] = sql_fetch_row($result2);



                if (is_array($tab[$ibid])) {
                    $ibid++;
                }

                sql_free_result($result2);
            }
        }

        @sql_free_result($result);

        return $tab;
    }

    /**
     * Prévisualise la présentation d'une news.
     *
     * @param string $title Titre de l'article
     * @param string $hometext Texte d'introduction
     * @param string $bodytext Texte complet (optionnel)
     * @param string $notes Notes supplémentaires (optionnel)
     * @return void
     */
    public static function themePreview(string $title, string $hometext, string $bodytext = '', string $notes = ''): void
    {
        echo "$title<br />" . Metalang::metaLang($hometext) . "<br />" . Metalang::metaLang($bodytext) . "<br />" . Metalang::metaLang($notes);
    }

    /**
     * Prépare et sérialise les news répondant aux critères spécifiés.
     *
     * @param string $op Type d'opération ('', 'categories', 'article', 'topics', 'news')
     * @param int|string $catid Identifiant de catégorie, topic ou article selon l'opération
     * @param int $marqeur Index de départ pour la sélection des news
     * @return void
     */
    public static function prepaAffNews(string $op, int|string $catid, int $marqeur)  // : void
    {
        global $topicname, $topicimage, $topictext, $datetime, $cookie; // global a revoir !

        $storyhome = Config::get('storie.storyhome');

        if (isset($cookie[3])) {
            $storynum = (int) $cookie[3];
        } else {
            $storynum = (int) $storyhome;
        }

        if ($op == 'categories') {
            sql_query("UPDATE " . sql_prefix('stories_cat') . " 
                    SET counter=counter+1 
                    WHERE catid='$catid'");

            settype($marqeur, 'integer');

            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = static::newsAff('libre', "WHERE catid='$catid' AND archive='0' ORDER BY sid DESC LIMIT $marqeur,$storynum", '', '-1');

            $storynum = sizeof($xtab);
        } elseif ($op == 'topics') {
            settype($marqeur, 'integer');

            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = static::newsAff("libre", "WHERE topic='$catid' AND archive='0' ORDER BY sid DESC LIMIT $marqeur,$storynum", "", "-1");

            $storynum = sizeof($xtab);
        } elseif ($op == 'news') {
            settype($marqeur, 'integer');

            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = static::newsAff('libre', "WHERE ihome!='1' AND archive='0' ORDER BY sid DESC LIMIT $marqeur,$storynum", '', '-1');

            $storynum = sizeof($xtab);
        } elseif ($op == 'article') {
            $xtab = static::newsAff('index', "WHERE ihome!='1' AND sid='$catid'", 1, '');
        } else {
            $xtab = static::newsAff('index', "WHERE ihome!='1' AND archive='0'", $storynum, '');
        }

        $story_limit = 0;

        while (($story_limit < $storynum) and ($story_limit < sizeof($xtab))) {

            list($s_sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[$story_limit];

            $story_limit++;

            $printP = '<a href="print.php?sid=' . $s_sid . '" class="me-3" title="' . translate('Page spéciale pour impression') . '" data-bs-toggle="tooltip" ><i class="fa fa-lg fa-print"></i></a>&nbsp;';
            $sendF = '<a href="friend.php?op=FriendSend&amp;sid=' . $s_sid . '" class="me-3" title="' . translate('Envoyer cet article à un ami') . '" data-bs-toggle="tooltip" ><i class="fa fa-lg fa-at"></i></a>';

            static::getTopics($s_sid);

            $title      = Language::affLangue(stripslashes($title));
            $hometext   = Language::affLangue(stripslashes($hometext));
            $notes      = Language::affLangue(stripslashes($notes));
            $bodycount  = strlen(strip_tags(Language::affLangue($bodytext), '<img>'));

            if ($bodycount > 0) {
                $bodycount = strlen(strip_tags(Language::affLangue($bodytext)));

                if ($bodycount > 0) {
                    $morelink[0] = Sanitize::wrh($bodycount) . ' ' . translate('caractères de plus');
                } else {
                    $morelink[0] = ' ';
                }

                $morelink[1] = ' <a href="article.php?sid=' . $s_sid . '" >' . translate('Lire la suite...') . '</a>';
            } else {
                $morelink[0] = '';
                $morelink[1] = '';
            }

            if ($comments == 0) {
                $morelink[2] = 0;
                $morelink[3] = '<a href="article.php?sid=' . $s_sid . '" class="me-3"><i class="far fa-comment fa-lg" title="' . translate('Commentaires ?') . '" data-bs-toggle="tooltip"></i></a>';
            } elseif ($comments == 1) {
                $morelink[2] = $comments;
                $morelink[3] = '<a href="article.php?sid=' . $s_sid . '" class="me-3"><i class="far fa-comment fa-lg" title="' . translate('Commentaire') . '" data-bs-toggle="tooltip"></i></a>';
            } else {
                $morelink[2] = $comments;
                $morelink[3] = '<a href="article.php?sid=' . $s_sid . '" class="me-3" ><i class="far fa-comment fa-lg" title="' . translate('Commentaires') . '" data-bs-toggle="tooltip"></i></a>';
            }

            $morelink[4] = $printP;
            $morelink[5] = $sendF;

            if ($catid != 0) {
                $resultm = sql_query("SELECT title 
                                    FROM " . sql_prefix('stories_cat') . " 
                                    WHERE catid='$catid'");

                list($title1) = sql_fetch_row($resultm);

                $title = $title;

                // Attention à cela aussi
                $morelink[6] = ' <a href="index.php?op=newcategory&amp;catid=' . $catid . '">&#x200b;' . Language::affLangue($title1) . '</a>';
            } else {
                $morelink[6] = '';
            }

            $news_tab[$story_limit]['aid']          = serialize($aid);
            $news_tab[$story_limit]['informant']    = serialize($informant);
            $news_tab[$story_limit]['datetime']     = serialize($time);
            $news_tab[$story_limit]['title']        = serialize($title);
            $news_tab[$story_limit]['counter']      = serialize($counter);
            $news_tab[$story_limit]['topic']        = serialize($topic);
            $news_tab[$story_limit]['hometext']     = serialize(Metalang::metaLang(Code::affCode($hometext)));
            $news_tab[$story_limit]['notes']        = serialize(Metalang::metaLang(Code::affCode($notes)));
            $news_tab[$story_limit]['morelink']     = serialize($morelink);
            $news_tab[$story_limit]['topicname']    = serialize($topicname);
            $news_tab[$story_limit]['topicimage']   = serialize($topicimage);
            $news_tab[$story_limit]['topictext']    = serialize($topictext);
            $news_tab[$story_limit]['id']           = serialize($s_sid);
        }

        if (isset($news_tab)) {
            return ($news_tab);
        }
    }

    #autodoc getTopics($s_sid) : Retourne le nom, l'image et le texte d'un topic ou False
    public static function getTopics($s_sid)
    {
        global $topicname, $topicimage, $topictext; // global a revoir !

        $sid = $s_sid;

        $result = sql_query("SELECT topic 
                            FROM " . sql_prefix('stories') . " 
                            WHERE sid='$sid'");

        if ($result) {
            list($topic) = sql_fetch_row($result);

            $result = sql_query("SELECT topicid, topicname, topicimage, topictext 
                                FROM " . sql_prefix('topics') . " 
                                WHERE topicid='$topic'");

            if ($result) {
                list($topicid, $topicname, $topicimage, $topictext) = sql_fetch_row($result);

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    #autodoc ultramode() : Génération des fichiers ultramode.txt et net2zone.txt dans /cache
    public static function ultramode()
    {
        $file = fopen('storage/cache/ultramode.txt', 'w');
        $file2 = fopen('storage/cache/net2zone.txt', 'w');

        fwrite($file, "General purpose self-explanatory file with news headlines\n");

        $storynum = Config::get('storie.storyhome');

        $xtab = static::newsAff('index', "WHERE ihome='0' AND archive='0'", Config::get('storie.storyhome'), '');

        $story_limit = 0;

        while (($story_limit < $storynum) and ($story_limit < sizeof($xtab))) {
            list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[$story_limit];

            $story_limit++;

            $rfile2 = sql_query("SELECT topictext, topicimage 
                                FROM " . sql_prefix('topics') . " 
                                WHERE topicid='$topic'");
            list($topictext, $topicimage) = sql_fetch_row($rfile2);


            $hometext = Metalang::metaLang(strip_tags($hometext));

            $nuke_url = Config::get('app.url');

            fwrite($file, "%%\n$title\n$nuke_url/article.php?sid=$sid\n$time\n$aid\n$topictext\n$hometext\n$topicimage\n");
            fwrite($file2, "<NEWS>\n<NBX>$topictext</NBX>\n<TITLE>" . stripslashes($title) . "</TITLE>\n<SUMMARY>$hometext</SUMMARY>\n<URL>$nuke_url/article.php?sid=$sid</URL>\n<AUTHOR>" . $aid . "</AUTHOR>\n</NEWS>\n\n");
        }

        fclose($file);
        fclose($file2);
    }
}
