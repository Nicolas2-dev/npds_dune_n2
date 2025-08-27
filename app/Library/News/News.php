<?php

namespace App\Library\News;


class News
{

    #autodoc ctrl_aff($ihome, $catid) : Gestion + fine des destinataires (-1, 0, 1, 2 -> 127, -127)
    function ctrl_aff($ihome, $catid = 0)
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
            $tab_groupe = valid_group($user);

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

    #autodoc news_aff($type_req, $sel, $storynum, $oldnum) : Une des fonctions fondamentales de NPDS / assure la gestion de la selection des News en fonctions des critères de publication
    function news_aff($type_req, $sel, $storynum, $oldnum)
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

            if (ctrl_aff($ihome, $catid)) {

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

    #autodoc themepreview($title, $hometext, $bodytext, $notes) : Permet de prévisualiser la présentation d'un NEW
    function themepreview($title, $hometext, $bodytext = '', $notes = '')
    {
        echo "$title<br />" . meta_lang($hometext) . "<br />" . meta_lang($bodytext) . "<br />" . meta_lang($notes);
    }

    #autodoc prepa_aff_news($op,$catid) : Prépare, serialize et stock dans un tableau les news répondant aux critères<br />$op="" ET $catid="" : les news // $op="categories" ET $catid="catid" : les news de la catégorie catid //  $op="article" ET $catid=ID_X : l'article d'ID X // Les news des sujets : $op="topics" ET $catid="topic"
    function prepa_aff_news($op, $catid, $marqeur)
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

            $xtab = news_aff('libre', "WHERE catid='$catid' AND archive='0' ORDER BY sid DESC LIMIT $marqeur,$storynum", '', '-1');

            $storynum = sizeof($xtab);

        } elseif ($op == 'topics') {
            settype($marqeur, 'integer');

            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = news_aff("libre", "WHERE topic='$catid' AND archive='0' ORDER BY sid DESC LIMIT $marqeur,$storynum", "", "-1");

            $storynum = sizeof($xtab);

        } elseif ($op == 'news') {
            settype($marqeur, 'integer');

            if (!isset($marqeur)) {
                $marqeur = 0;
            }

            $xtab = news_aff('libre', "WHERE ihome!='1' AND archive='0' ORDER BY sid DESC LIMIT $marqeur,$storynum", '', '-1');

            $storynum = sizeof($xtab);

        } elseif ($op == 'article') {
            $xtab = news_aff('index', "WHERE ihome!='1' AND sid='$catid'", 1, '');
        } else {
            $xtab = news_aff('index', "WHERE ihome!='1' AND archive='0'", $storynum, '');
        }

        $story_limit = 0;

        while (($story_limit < $storynum) and ($story_limit < sizeof($xtab))) {

            list($s_sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[$story_limit];

            $story_limit++;

            $printP = '<a href="print.php?sid=' . $s_sid . '" class="me-3" title="' . translate('Page spéciale pour impression') . '" data-bs-toggle="tooltip" ><i class="fa fa-lg fa-print"></i></a>&nbsp;';
            $sendF = '<a href="friend.php?op=FriendSend&amp;sid=' . $s_sid . '" class="me-3" title="' . translate('Envoyer cet article à un ami') . '" data-bs-toggle="tooltip" ><i class="fa fa-lg fa-at"></i></a>';

            getTopics($s_sid);

            $title      = aff_langue(stripslashes($title));
            $hometext   = aff_langue(stripslashes($hometext));
            $notes      = aff_langue(stripslashes($notes));
            $bodycount  = strlen(strip_tags(aff_langue($bodytext), '<img>'));

            if ($bodycount > 0) {
                $bodycount = strlen(strip_tags(aff_langue($bodytext)));

                if ($bodycount > 0) {
                    $morelink[0] = wrh($bodycount) . ' ' . translate('caractères de plus');
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
                $morelink[6] = ' <a href="index.php?op=newcategory&amp;catid=' . $catid . '">&#x200b;' . aff_langue($title1) . '</a>';
            } else {
                $morelink[6] = '';
            }

            $news_tab[$story_limit]['aid']          = serialize($aid);
            $news_tab[$story_limit]['informant']    = serialize($informant);
            $news_tab[$story_limit]['datetime']     = serialize($time);
            $news_tab[$story_limit]['title']        = serialize($title);
            $news_tab[$story_limit]['counter']      = serialize($counter);
            $news_tab[$story_limit]['topic']        = serialize($topic);
            $news_tab[$story_limit]['hometext']     = serialize(meta_lang(aff_code($hometext)));
            $news_tab[$story_limit]['notes']        = serialize(meta_lang(aff_code($notes)));
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
    function getTopics($s_sid)
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
    function ultramode()
    {
        global $nuke_url, $storyhome;

        $file = fopen('storage/cache/ultramode.txt', 'w');
        $file2 = fopen('storage/cache/net2zone.txt', 'w');

        fwrite($file, "General purpose self-explanatory file with news headlines\n");

        $storynum = $storyhome;

        $xtab = news_aff('index', "WHERE ihome='0' AND archive='0'", $storyhome, '');

        $story_limit = 0;

        while (($story_limit < $storynum) and ($story_limit < sizeof($xtab))) {
            list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[$story_limit];

            $story_limit++;

            $rfile2 = sql_query("SELECT topictext, topicimage 
                                FROM " . sql_prefix('topics') . " 
                                WHERE topicid='$topic'");
            list($topictext, $topicimage) = sql_fetch_row($rfile2);


            $hometext = meta_lang(strip_tags($hometext));

            fwrite($file, "%%\n$title\n$nuke_url/article.php?sid=$sid\n$time\n$aid\n$topictext\n$hometext\n$topicimage\n");
            fwrite($file2, "<NEWS>\n<NBX>$topictext</NBX>\n<TITLE>" . stripslashes($title) . "</TITLE>\n<SUMMARY>$hometext</SUMMARY>\n<URL>$nuke_url/article.php?sid=$sid</URL>\n<AUTHOR>" . $aid . "</AUTHOR>\n</NEWS>\n\n");
        }

        fclose($file);
        fclose($file2);
    }

}
