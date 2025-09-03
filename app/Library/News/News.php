<?php

namespace App\Library\News;

use App\Library\String\Sanitize;
use App\Library\Code\Code;
use App\Library\Groupe\Groupe;
use App\Library\Language\Language;
use App\Library\Metalang\Metalang;


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
        global $user;

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

    /**
     * Récupère les news selon le type de requête et les critères de sélection.
     *
     * @param string $type_req Type de sélection ('index', 'old_news', 'big_story', 'big_topic', 'libre', 'archive')
     * @param string $sel Clause SQL WHERE ou similaire pour filtrer les news
     * @param int $storynum Nombre d'articles à récupérer
     * @param int|string $oldnum Ancien nombre d'articles (utilisé pour certains calculs)
     * @return array Tableau contenant les news récupérées
     */
    public static function newsAff(string $type_req, string $sel, int $storynum, int|string $oldnum): array
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
        global $storyhome, $topicname, $topicimage, $topictext, $datetime, $cookie;

        if (isset($cookie[3])) {
            $storynum = $cookie[3];
        } else {
            $storynum = $storyhome;
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
        global $topicname, $topicimage, $topictext;

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
        global $nuke_url, $storyhome;

        $file = fopen('storage/cache/ultramode.txt', 'w');
        $file2 = fopen('storage/cache/net2zone.txt', 'w');

        fwrite($file, "General purpose self-explanatory file with news headlines\n");

        $storynum = $storyhome;

        $xtab = static::newsAff('index', "WHERE ihome='0' AND archive='0'", $storyhome, '');

        $story_limit = 0;

        while (($story_limit < $storynum) and ($story_limit < sizeof($xtab))) {
            list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[$story_limit];

            $story_limit++;

            $rfile2 = sql_query("SELECT topictext, topicimage 
                                FROM " . sql_prefix('topics') . " 
                                WHERE topicid='$topic'");
            list($topictext, $topicimage) = sql_fetch_row($rfile2);


            $hometext = Metalang::metaLang(strip_tags($hometext));

            fwrite($file, "%%\n$title\n$nuke_url/article.php?sid=$sid\n$time\n$aid\n$topictext\n$hometext\n$topicimage\n");
            fwrite($file2, "<NEWS>\n<NBX>$topictext</NBX>\n<TITLE>" . stripslashes($title) . "</TITLE>\n<SUMMARY>$hometext</SUMMARY>\n<URL>$nuke_url/article.php?sid=$sid</URL>\n<AUTHOR>" . $aid . "</AUTHOR>\n</NEWS>\n\n");
        }

        fclose($file);
        fclose($file2);
    }
}
