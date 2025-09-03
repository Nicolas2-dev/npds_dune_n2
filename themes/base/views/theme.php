<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* DYNAMIC THEME engine for NPDS                                        */
/* NPDS Copyright (c) 2002-2025 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

global $meta_glossaire; // pourquoi un global $meta_glossaire içi pffff 

/**
 * Extrait une variable locale marquée par !var! dans le texte.
 *
 * @param string $Xcontent Contenu texte contenant éventuellement !var!VariableName
 * @return string|null Retourne le nom de la variable si trouvé, sinon null
 */
function local_var(string $Xcontent): ?string
{
    if (strstr($Xcontent, '!var!')) {
        $deb = strpos($Xcontent, '!var!', 0) + 5;
        $fin = strpos($Xcontent, ' ', $deb);

        if ($fin) {
            $H_var = substr($Xcontent, $deb, $fin - $deb);
        } else {
            $H_var = substr($Xcontent, $deb);
        }

        return $H_var;
    }

    return null;
}

/**
 * Génère le contenu d'une news pour l'index.
 *
 * @param string $aid ID de l'auteur
 * @param string $informant Nom de l'émetteur
 * @param int $time Timestamp
 * @param string $title Titre de l'article
 * @param int $counter Nombre de lectures
 * @param string|int $topic ID du topic
 * @param string $thetext Contenu de l'article
 * @param string $notes Notes associées
 * @param array $morelink Liens supplémentaires (read more, comments, etc.)
 * @param string $topicname Nom du topic
 * @param string $topicimage Image du topic
 * @param string $topictext Description du topic
 * @param string|int $id ID de l'article
 * @return void
 */
function themeindex(
    string      $aid,
    string      $informant,
    int         $time,
    string      $title,
    int         $counter,
    string|int  $topic,
    string      $thetext,
    string      $notes,
    array       $morelink,
    string      $topicname,
    string      $topicimage,
    string      $topictext,
    string|int  $id
): void {
    global $tipath, $theme;

    $inclusion = false;

    if (file_exists('themes/' . $theme . '/views/partials/news/index-news.php')) {
        $inclusion = 'themes/' . $theme . '/views/partials/news/index-news.php';
    } elseif (file_exists('themes/base/views/partials/news/index-news.php')) {
        $inclusion = 'themes/base/views/partials/news/index-news.php';
    } else {
        echo 'index-news.php manquant / not find !<br />';
        die();
    }

    $H_var = local_var($thetext);

    if ($H_var != '') {
        ${$H_var} = true;

        $thetext = str_replace("!var!$H_var", "", $thetext);
    }

    if ($notes != '') {
        $notes = '<div class="note">' . translate("Note") . ' : ' . $notes . '</div>';
    }

    ob_start();
    include $inclusion;
    $Xcontent = ob_get_contents();
    ob_end_clean();

    $lire_la_suite = '';

    if ($morelink[0]) {
        $lire_la_suite = $morelink[1] . ' ' . $morelink[0] . ' | ';
    }

    $commentaire = '';

    if ($morelink[2]) {
        $commentaire = $morelink[2] . ' ' . $morelink[3] . ' | ';
    } else {
        $commentaire = $morelink[3] . ' | ';
    }

    $categorie = '';

    if ($morelink[6]) {
        $categorie = ' : ' . $morelink[6];
    }

    $morel = $lire_la_suite . $commentaire . $morelink[4] . ' ' . $morelink[5] . $categorie;

    $Xsujet = '';

    if ($topicimage != '') {
        if (!$imgtmp = themeImage('topics/' . $topicimage)) {
            $imgtmp = $tipath . $topicimage;
        }

        $Xsujet = '<a href="search.php?query=&amp;topic=' . $topic . '"><img class="img-fluid" src="' . $imgtmp . '" alt="' . translate("Rechercher dans") . ' : ' . $topicname . '" title="' . translate("Rechercher dans") . ' : ' . $topicname . '<hr />' . $topictext . '" data-bs-toggle="tooltip" data-bs-html="true" /></a>';
    } else {
        $Xsujet = '<a href="search.php?query=&amp;topic=' . $topic . '"><span class="badge bg-secondary h1" title="' . translate("Rechercher dans") . ' : ' . $topicname . '<hr />' . $topictext . '" data-bs-toggle="tooltip" data-bs-html="true">' . $topicname . '</span></a>';
    }

    $npds_METALANG_words = array(
        "'!N_publicateur!'i"    => $aid,
        "'!N_emetteur!'i"       => userpopover($informant, 40, 2) . '<a href="user.php?op=userinfo&amp;uname=' . $informant . '">' . $informant . '</a>',
        "'!N_date!'i"           => Date::formatTimes($time, IntlDateFormatter::FULL, IntlDateFormatter::SHORT),
        "'!N_date_y!'i"         => Date::getPartOfTime($time, 'yyyy'),
        "'!N_date_m!'i"         => Date::getPartOfTime($time, 'MMMM'),
        "'!N_date_d!'i"         => Date::getPartOfTime($time, 'd'),
        "'!N_date_h!'i"         => Date::formatTimes($time, IntlDateFormatter::NONE, IntlDateFormatter::MEDIUM),
        "'!N_print!'i"          => $morelink[4],
        "'!N_friend!'i"         => $morelink[5],
        "'!N_nb_carac!'i"       => $morelink[0],
        "'!N_read_more!'i"      => $morelink[1],
        "'!N_nb_comment!'i"     => $morelink[2],
        "'!N_link_comment!'i"   => $morelink[3],
        "'!N_categorie!'i"      => $morelink[6],
        "'!N_titre!'i"          => $title,
        "'!N_texte!'i"          => $thetext,
        "'!N_id!'i"             => $id,
        "'!N_sujet!'i"          => $Xsujet,
        "'!N_note!'i"           => $notes,
        "'!N_nb_lecture!'i"     => $counter,
        "'!N_suite!'i"          => $morel
    );

    echo Metalang::metaLang(Language::affLangue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
}

/**
 * Génère le contenu détaillé d'un article.
 *
 * @param string $aid ID de l'auteur
 * @param string $informant Nom de l'émetteur
 * @param int $time Timestamp
 * @param string $title Titre de l'article
 * @param string $thetext Contenu de l'article
 * @param string|int $topic ID du topic
 * @param string $topicname Nom du topic
 * @param string $topicimage Image du topic
 * @param string $topictext Description du topic
 * @param string|int $id ID de l'article
 * @param int|null $previous_sid ID de l'article précédent
 * @param int|null $next_sid ID de l'article suivant
 * @param string|null $archive Archive associée
 * @return void
 */
function themearticle(
    string $aid,
    string      $informant,
    int         $time,
    string      $title,
    string      $thetext,
    string|int  $topic,
    string      $topicname,
    string      $topicimage,
    string      $topictext,
    string|int  $id,
    ?int        $previous_sid,
    ?int        $next_sid,
    ?string     $archive
): void {
    global $tipath, $theme, $counter, $boxtitle, $boxstuff;

    $inclusion = false;

    if (file_exists("themes/" . $theme . "/views/partials/news/detail-news.php")) {
        $inclusion = "themes/" . $theme . "/views/partials/news/detail-news.php";
    } elseif (file_exists("themes/base/views/partials/news/detail-news.php")) {
        $inclusion = "themes/base/views/partials/news/detail-news.php";
    } else {
        echo 'detail-news.php manquant / not find !<br />';
        die();
    }

    $H_var = local_var($thetext);

    if ($H_var != '') {
        ${$H_var} = true;

        $thetext = str_replace("!var!$H_var", '', $thetext);
    }

    ob_start();
    include $inclusion;
    $Xcontent = ob_get_contents();
    ob_end_clean();

    if ($previous_sid) {
        $prevArt = '<a href="article.php?sid=' . $previous_sid . '&amp;archive=' . $archive . '" ><i class="fa fa-chevron-left fa-lg me-2" title="' . translate("Précédent") . '" data-bs-toggle="tooltip"></i><span class="d-none d-sm-inline">' . translate("Précédent") . '</span></a>';
    } else {
        $prevArt = '';
    }

    if ($next_sid) {
        $nextArt = '<a href="article.php?sid=' . $next_sid . '&amp;archive=' . $archive . '" ><span class="d-none d-sm-inline">' . translate("Suivant") . '</span><i class="fa fa-chevron-right fa-lg ms-2" title="' . translate("Suivant") . '" data-bs-toggle="tooltip"></i></a>';
    } else {
        $nextArt = '';
    }

    $printP = '<a href="print.php?sid=' . $id . '" title="' . translate("Page spéciale pour impression") . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-print"></i></a>';
    $sendF = '<a href="friend.php?op=FriendSend&amp;sid=' . $id . '" title="' . translate("Envoyer cet article à un ami") . '" data-bs-toggle="tooltip"><i class="fa fa-2x fa-at"></i></a>';

    if (!$imgtmp = themeImage('topics/' . $topicimage)) {
        $imgtmp = $tipath . $topicimage;
    }

    $timage = $imgtmp;

    $npds_METALANG_words = array(
        "'!N_publicateur!'i"        => $aid,
        "'!N_emetteur!'i"           => userpopover($informant, 40, 2) . '<a href="user.php?op=userinfo&amp;uname=' . $informant . '"><span class="">' . $informant . '</span></a>',
        "'!N_date!'i"               => Date::formatTimes($time, IntlDateFormatter::FULL, IntlDateFormatter::SHORT),
        "'!N_date_y!'i"             => Date::getPartOfTime($time, 'yyyy'),
        "'!N_date_m!'i"             => Date::getPartOfTime($time, 'MMMM'),
        "'!N_date_d!'i"             => Date::getPartOfTime($time, 'd'),
        "'!N_date_h!'i"             => Date::formatTimes($time, IntlDateFormatter::NONE, IntlDateFormatter::MEDIUM),
        "'!N_print!'i"              => $printP,
        "'!N_friend!'i"             => $sendF,
        "'!N_boxrel_title!'i"       => $boxtitle,
        "'!N_boxrel_stuff!'i"       => $boxstuff,
        "'!N_titre!'i"              => $title,
        "'!N_id!'i"                 => $id,
        "'!N_previous_article!'i"   => $prevArt,
        "'!N_next_article!'i"       => $nextArt,
        "'!N_sujet!'i"              => '<a href="search.php?query=&amp;topic=' . $topic . '"><img class="img-fluid" src="' . $timage . '" alt="' . translate("Rechercher dans") . '&nbsp;' . $topictext . '" /></a>',
        "'!N_texte!'i"              => $thetext,
        "'!N_nb_lecture!'i"         => $counter
    );

    echo Metalang::metaLang(Language::affLangue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
}

/**
 * Génère un bloc latéral pour le thème.
 *
 * @param string $title Titre du bloc
 * @param string $content Contenu HTML du bloc
 * @return void
 */
function themesidebox(string $title, string $content): void
{
    global $theme, $B_class_title, $B_class_content, $bloc_side, $htvar;

    $inclusion = false;

    if (file_exists('themes/' . $theme . '/views/partials/block/bloc-right.php') && ($bloc_side == 'RIGHT')) {
        $inclusion = 'themes/' . $theme . '/views/partials/block/bloc-right.php';
    }

    if (file_exists('themes/' . $theme . '/views/partials/block/bloc-left.php') && ($bloc_side == 'LEFT')) {
        $inclusion = 'themes/' . $theme . '/views/partials/block/bloc-left.php';
    }

    if (!$inclusion) {
        if (file_exists('themes/' . $theme . '/views/partials/block/bloc.php')) {
            $inclusion = 'themes/' . $theme . '/views/partials/block/bloc.php';
        } elseif (file_exists('themes/base/views/partials/block/bloc.php')) {
            $inclusion = 'themes/base/views/partials/block/bloc.php';
        } else {
            echo 'bloc.php manquant / not find !<br />';
            die();
        }
    }

    ob_start();
    include $inclusion;
    $Xcontent = ob_get_contents();
    ob_end_clean();

    if ($title == 'no-title') {
        $Xcontent = str_replace('<div class="LB_title">!B_title!</div>', '', $Xcontent);
        $title = '';
    }

    $npds_METALANG_words = array(
        "'!B_title!'i"          => $title,
        "'!B_class_title!'i"    => isset($B_class_title) && $B_class_title !== '' ? $B_class_title : 'noclass',
        "'!B_class_content!'i"  => isset($B_class_content) && $B_class_content !== '' ? $B_class_content : 'noclass',
        "'!B_content!'i"        => $content
    );

    echo $htvar;

    echo Metalang::metaLang(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent));

    echo '</div>';
}

/**
 * Affiche l'éditorial d'un thème.
 *
 * Cherche le fichier `editorial.php` dans le thème actif, puis dans le thème de base.  
 * Remplace le placeholder `!editorial_content!` par le contenu fourni et applique la fonction `Metalang::metaLang()` et `Language::affLangue()`.
 *
 * @param string $content Le contenu à insérer dans l'éditorial.
 * @return string|false Le chemin du fichier inclus, ou false si non trouvé.
 */
function themedito(string $content): string|false
{
    global $theme;

    $inclusion = false;

    if (file_exists('themes/' . $theme . '/views/partials/edito/editorial.php')) {
        $inclusion = 'themes/' . $theme . '/views/partials/edito/editorial.php';
    } elseif (file_exists('themes/base/views/partials/edito/editorial.php')) {
        $inclusion = 'themes/base/views/partials/edito/editorial.php';
    } else {
        echo 'editorial.php manquant / not find !<br />';
        die();
    }

    if ($inclusion) {
        ob_start();
        include $inclusion;
        $Xcontent = ob_get_contents();
        ob_end_clean();

        $npds_METALANG_words = array(
            "'!editorial_content!'i"    => $content
        );

        echo metaLang(Language::affLangue(preg_replace(array_keys($npds_METALANG_words), array_values($npds_METALANG_words), $Xcontent)));
    }

    return $inclusion;
}

/**
 * Génère un avatar ou un popover utilisateur.
 *
 * Selon la valeur de `$avpop` :
 * - 1 : Affiche l'avatar seul.
 * - 2 : Affiche l'avatar avec un popover contenant les informations et liens de l'utilisateur.
 *
 * @param string $who Nom de l'utilisateur.
 * @param int $dim Taille de l'avatar (détermine la classe CSS `n-ava-$dim`).
 * @param int $avpop Mode d'affichage : 1 pour avatar seul, 2 pour popover.
 * @return string|null HTML de l'avatar ou du popover, ou null si l'utilisateur n'existe pas.
 */
function userpopover(string $who, int $dim, int $avpop): ?string
{
    global $short_user, $user;

    $result = sql_query("SELECT uname 
                         FROM " . sql_prefix('users') . " 
                         WHERE uname ='$who'");

    include_once 'functions.php';

    if (sql_num_rows($result)) {

        $temp_user = Forum::getUserData($who);

        $socialnetworks = array();
        $posterdata_extend = array();
        $res_id = array();

        $my_rs = '';

        if (!$short_user) {
            if ($temp_user['uid'] != 1) {

                $posterdata_extend = Forum::getUserDataExtendFromId($temp_user['uid']);

                include 'modules/reseaux-sociaux/config/config.php';
                include 'modules/geoloc/config/config.php';

                if ($user or Auth::autorisation(-127)) {
                    if ($posterdata_extend['M2'] != '') {
                        $socialnetworks = explode(';', $posterdata_extend['M2']);

                        foreach ($socialnetworks as $socialnetwork) {
                            $res_id[] = explode('|', $socialnetwork);
                        }

                        sort($res_id);
                        sort($rs);

                        foreach ($rs as $v1) {
                            foreach ($res_id as $y1) {
                                $k = array_search($y1[0], $v1);

                                if (false !== $k) {
                                    $my_rs .= '<a class="me-2 " href="';

                                    if ($v1[2] == 'skype') {
                                        $my_rs .= $v1[1] . $y1[1] . '?chat';
                                    } else {
                                        $my_rs .= $v1[1] . $y1[1];
                                    }

                                    $my_rs .= '" target="_blank"><i class="fab fa-' . $v1[2] . ' fa-lg fa-fw mb-2"></i></a> ';
                                    break;
                                } else {
                                    $my_rs .= '';
                                }
                            }
                        }
                    }
                }
            }
        }

        settype($ch_lat, 'string');

        $useroutils = '';

        if ($user or Auth::autorisation(-127)) {
            if ($temp_user['uid'] != 1 and $temp_user['uid'] != '') {
                $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="user.php?op=userinfo&amp;uname=' . $temp_user['uname'] . '" target="_blank" title="' . translate("Profil") . '" ><i class="fa fa-lg fa-user align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Profil") . '</span></a></li>';
            }

            if ($temp_user['uid'] != 1 and $temp_user['uid'] != '') {
                $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="powerpack.php?op=instant_message&amp;to_userid=' . urlencode($temp_user['uname']) . '" title="' . translate("Envoyer un message interne") . '" ><i class="far fa-lg fa-envelope align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Message") . '</span></a></li>';
            }

            if ($temp_user['femail'] != '') {
                $useroutils .= '<li><a class="dropdown-item  text-center text-md-start" href="mailto:' . antiSpam($temp_user['femail'], 1) . '" target="_blank" title="' . translate("Email") . '" ><i class="fa fa-at fa-lg align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Email") . '</span></a></li>';
            }

            if ($temp_user['uid'] != 1 and array_key_exists($ch_lat, $posterdata_extend)) {
                if ($posterdata_extend[$ch_lat] != '') {
                    $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="modules.php?ModPath=geoloc&amp;ModStart=geoloc&op=u' . $temp_user['uid'] . '" title="' . translate("Localisation") . '" ><i class="fas fa-map-marker-alt fa-lg align-middle fa-fw">&nbsp;</i><span class="ms-2 d-none d-md-inline">' . translate("Localisation") . '</span></a></li>';
                }
            }
        }

        if ($temp_user['url'] != '') {
            $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="' . $temp_user['url'] . '" target="_blank" title="' . translate("Visiter ce site web") . '"><i class="fas fa-external-link-alt fa-lg align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Visiter ce site web") . '</span></a></li>';
        }

        if ($temp_user['mns']) {
            $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="minisite.php?op=' . $temp_user['uname'] . '" target="_blank" title="' . translate("Visitez le minisite") . '" ><i class="fa fa-lg fa-desktop align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Visitez le minisite") . '</span></a></li>';
        }

        if (stristr($temp_user['user_avatar'], 'users_private')) {
            $imgtmp = $temp_user['user_avatar'];
        } else {
            $imgtmp = themeImage('forum/avatar/' . $temp_user['user_avatar']) ?: 'assets/images/forum/avatar/' . $temp_user['user_avatar'];
        }

        $userpop = $avpop == 1
            ? '<img class="btn-outline-primary img-thumbnail img-fluid n-ava-' . $dim . ' me-2" src="' . $imgtmp . '" alt="' . $temp_user['uname'] . '" loading="lazy" />'
            : '<div class="dropdown d-inline-block me-4 dropend">
                <a class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <img class=" btn-outline-primary img-fluid n-ava-' . $dim . ' me-0" src="' . $imgtmp . '" alt="' . $temp_user['uname'] . '" loading="lazy" />
                </a>
                <ul class="dropdown-menu" data-bs-theme="light" >
                    <li><span class="dropdown-item-text text-center py-0 my-0">
                        <img class="btn-outline-primary img-thumbnail img-fluid n-ava-64 me-2" src="' . $imgtmp . '" alt="' . $temp_user['uname'] . '" loading="lazy" />
                    </span></li>
                    <li><h6 class="dropdown-header text-center py-0 my-0">' . $who . '</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    ' . $useroutils . '
                    <li><hr class="dropdown-divider"></li>
                    <li><div class="mx-auto text-center" style="max-width:170px;">' . $my_rs . '</div>
                </ul>
                </div>';

        return $userpop;
    }

    return null;
}
