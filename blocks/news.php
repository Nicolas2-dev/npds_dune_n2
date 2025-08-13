<?php

#autodoc oldNews($storynum) : Bloc Anciennes News <br />=> syntaxe <br />function#oldNews<br />params#$storynum,lecture (affiche le NB de lecture) - facultatif
function oldNews($storynum, $typ_aff = '')
{
    global $oldnum, $storyhome, $categories, $cat, $user, $cookie, $language;

    $boxstuff = '<ul class="list-group">';
    $storynum = isset($cookie[3]) ? $cookie[3] : $storyhome;

    if (($categories == 1) and ($cat != ''))
        $sel = $user ? "WHERE catid='$cat'" : "WHERE catid='$cat' AND ihome=0";
    else
        $sel = $user ? '' : "WHERE ihome=0";

    $sel =  "WHERE ihome=0"; // en dur pour test

    $vari = 0;

    $xtab = news_aff('old_news', $sel, $storynum, $oldnum);

    $story_limit = 0;
    $time2 = 0;
    $a = 0;

    while (($story_limit < $oldnum) and ($story_limit < sizeof($xtab))) {
        list($sid, $title, $time, $comments, $counter) = $xtab[$story_limit];

        $story_limit++;

        $date_au_format = formatTimes($time, IntlDateFormatter::FULL);

        $comments = $typ_aff == 'lecture' ?
            '<span class="badge rounded-pill bg-secondary ms-1" title="' . translate("Lu") . '" data-bs-toggle="tooltip">' . $counter . '</span>' : '';

        if ($time2 == $date_au_format)
            $boxstuff .= '<li class="list-group-item list-group-item-action d-inline-flex justify-content-between align-items-center"><a class="n-ellipses" href="article.php?sid=' . $sid . '">' . aff_langue($title) . '</a>' . $comments . '</li>';
        else {
            if ($a == 0) {
                $boxstuff .= '<li class="list-group-item fs-6">' . $date_au_format . '</li>
            <li class="list-group-item list-group-item-action d-inline-flex justify-content-between align-items-center"><a href="article.php?sid=' . $sid . '">' . aff_langue($title) . '</a>' . $comments . '</li>';

                $time2 = $date_au_format;

                $a = 1;
            } else {
                $boxstuff .= '<li class="list-group-item fs-6">' . $date_au_format . '</li>
            <li class="list-group-item list-group-item-action d-inline-flex justify-content-between align-items-center"><a href="article.php?sid=' . $sid . '">' . aff_langue($title) . '</a>' . $comments . '</li>';

                $time2 = $date_au_format;
            }
        }

        $vari++;

        if ($vari == $oldnum) {
            //$storynum = isset($cookie[3]) ? $cookie[3] : $storyhome ;
            $min = $oldnum; // + $storynum;

            $boxstuff .= '<li class="text-center mt-3"><a href="search.php?min=' . $min . '&amp;type=stories&amp;category=' . $cat . '"><strong>' . translate("Articles plus anciens") . '</strong></a></li>';
        }
    }

    $boxstuff .= '</ul>';

    if (strpos($boxstuff, '<li') === false)
        $boxstuff = '';

    global $block_title;
    $boxTitle = $block_title == '' ? translate("Anciens articles") : $block_title;

    themesidebox($boxTitle, $boxstuff);
}

#autodoc bigstory() : Bloc BigStory <br />=> syntaxe : function#bigstory
function bigstory()
{
    global $cookie; //no need ?

    $content = '';

    $tdate = getPartOfTime(time(), 'yyyy-MM-dd');

    $xtab = news_aff("big_story", "WHERE (time LIKE '%$tdate%')", 1, 1);

    if (sizeof($xtab))
        list($fsid, $ftitle) = $xtab[0];
    else {
        $fsid = '';
        $ftitle = '';
    }

    $content .= ($fsid == '' and $ftitle == '')
        ? '<span class="fw-semibold">' . translate("Il n'y a pas encore d'article du jour.") . '</span>'
        : '<span class="fw-semibold">' . translate("L'article le plus consulté aujourd'hui est :") . '</span><br /><br /><a href="article.php?sid=' . $fsid . '">' . aff_langue($ftitle) . '</a>';

    global $block_title;
    $boxtitle = $block_title == '' ? translate("Article du Jour") : $block_title;

    themesidebox($boxtitle, $content);
}

#autodoc category() : Bloc de gestion des catégories <br />=> syntaxe : function#category
function category()
{
    global sql_prefix(''), $cat, $language;

    $result = sql_query("SELECT catid, title FROM " . sql_prefix('') . "stories_cat ORDER BY title");
    $numrows = sql_num_rows($result);

    if ($numrows == 0)
        return;
    else {
        $boxstuff = '<ul>';

        while (list($catid, $title) = sql_fetch_row($result)) {
            $result2 = sql_query("SELECT sid FROM " . sql_prefix('') . "stories WHERE catid='$catid' LIMIT 0,1");
            $numrows = sql_num_rows($result2);

            if ($numrows > 0) {
                $res = sql_query("SELECT time FROM " . sql_prefix('') . "stories WHERE catid='$catid' ORDER BY sid DESC LIMIT 0,1");
                list($time) = sql_fetch_row($res);

                $boxstuff .= $cat == $catid
                    ? '<li class="my-2"><strong>' . aff_langue($title) . '</strong></li>'
                    : '<li class="list-group-item list-group-item-action hyphenate my-2"><a href="index.php?op=newcategory&amp;catid=' . $catid . '" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="right" title="' . translate("Dernière contribution") . ' <br />' . formatTimes($time) . ' ">' . aff_langue($title) . '</a></li>';
            }
        }

        $boxstuff .= '</ul>';

        global $block_title;
        $title = $block_title == '' ? translate("Catégories") : $block_title;

        themesidebox($title, $boxstuff);
    }
}

#autodoc bloc_rubrique() : Bloc des Rubriques <br />=> syntaxe : function#bloc_rubrique
function bloc_rubrique()
{
    global sql_prefix(''), $language, $user;

    $result = sql_query("SELECT rubid, rubname, ordre FROM " . sql_prefix('') . "rubriques WHERE enligne='1' AND rubname<>'divers' ORDER BY ordre");

    $boxstuff = '<ul>';

    while (list($rubid, $rubname) = sql_fetch_row($result)) {

        $title = aff_langue($rubname);

        $result2 = sql_query("SELECT secid, secname, userlevel, ordre FROM " . sql_prefix('') . "sections WHERE rubid='$rubid' ORDER BY ordre");

        $boxstuff .= '<li><strong>' . $title . '</strong></li>';

        //$ibid++;//??? only for notice ???

        while (list($secid, $secname, $userlevel) = sql_fetch_row($result2)) {
            $query3 = "SELECT artid FROM " . sql_prefix('') . "seccont WHERE secid='$secid'";
            $result3 = sql_query($query3);
            $nb_article = sql_num_rows($result3);

            if ($nb_article > 0) {
                $boxstuff .= '<ul>';

                $tmp_auto = explode(',', $userlevel);

                foreach ($tmp_auto as $userlevel) {
                    $okprintLV1 = autorisation($userlevel);

                    if ($okprintLV1)
                        break;
                }

                if ($okprintLV1) {
                    $sec = aff_langue($secname);

                    $boxstuff .= '<li><a href="sections.php?op=listarticles&amp;secid=' . $secid . '">' . $sec . '</a></li>';
                }

                $boxstuff .= '</ul>';
            }
        }
    }

    $boxstuff .= '</ul>';

    global $block_title;
    $title = $block_title == '' ? translate("Rubriques") : $block_title;

    themesidebox($title, $boxstuff);
}
