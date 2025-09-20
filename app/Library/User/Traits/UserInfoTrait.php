<?php

namespace App\Library\User\Traits;

use IntlDateFormatter;
use App\Support\Facades\Date;
use App\Support\Facades\News;
use App\Support\Facades\Spam;
use App\Support\Facades\User;
use App\Support\Facades\Forum;
use App\Support\Facades\Media;
use App\Support\Facades\Theme;
use App\Support\Security\Hack;
use App\Support\Facades\Groupe;
use App\Support\Facades\Smilies;
use App\Support\Facades\Language;
use App\Library\Metalang\Metalang;


trait UserInfoTrait
{

    /*
    switch ($op) {

        case 'userinfo':
            if (($member_list == 1) and ((!isset($user)) and (!isset($admin)))) {
                Header('Location: index.php');
            }

            if ($uname != '') {
                $this->userinfo($uname);
            } else {
                $this->main($user);
            }
            break;
    }
    */

    public function userInfo($uname)
    {
        global $user, $admin, $sitename, $smilies, $short_user;
        global $name, $email, $url, $bio, $user_avatar, $user_from, $user_occ, $user_intrest, $user_sig, $user_journal, $C7, $C8;

        $uname = Hack::removeHack($uname);

        $result = sql_query("SELECT uid, name, femail, url, bio, user_avatar, user_from, user_occ, user_intrest, user_sig, user_journal, mns 
                            FROM " . sql_prefix('users') . " 
                            WHERE uname='$uname'");

        list($uid, $name, $femail, $url, $bio, $user_avatar, $user_from, $user_occ, $user_intrest, $user_sig, $user_journal, $mns) = sql_fetch_row($result);

        if (!$uid) {
            header('location: index.php');
        }

        global $cookie;

        //include 'header.php';

        $email          = Hack::removeHack($femail);
        $name           = stripslashes(Hack::removeHack($name));
        $url            = Hack::removeHack($url);
        $bio            = stripslashes(Hack::removeHack($bio));
        $user_from      = stripslashes(Hack::removeHack($user_from));
        $user_occ       = stripslashes(Hack::removeHack($user_occ));
        $user_intrest   = stripslashes(Hack::removeHack($user_intrest));
        $user_sig       = nl2br(Hack::removeHack($user_sig));
        $user_journal   = stripslashes(Hack::removeHack($user_journal));

        $op = 'userinfo';

        if (stristr($user_avatar, 'users_private')) {
            $direktori = '';
        } else {
            global $theme;
            $direktori = 'assets/images/forum/avatar/';

            if (function_exists('theme_image')) {
                if (Theme::themeImage('forum/avatar/blank.gif')) {
                    $direktori = 'themes/' . $theme . '/assets/images/forum/avatar/';
                }
            }
        }

        $socialnetworks     = [];
        $posterdata_extend  = [];
        $res_id             = [];

        $my_rs = '';

        $posterdata_extend = Forum::getUserDataExtendFromId($uid);

        if (!$short_user) {
            include 'modules/reseaux-sociaux/config/config.php';

            if (array_key_exists('M2', $posterdata_extend)) {
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
                            $my_rs .= '<a class="me-3" href="';

                            if ($v1[2] == 'skype') {
                                $my_rs .= $v1[1] . $y1[1] . '?chat';
                            } else {
                                $my_rs .= $v1[1] . $y1[1];
                            }

                            $my_rs .= '" target="_blank"><i class="fab fa-' . $v1[2] . ' fa-2x"></i></a> ';
                            break;
                        } else {
                            $my_rs .= '';
                        }
                    }
                }
            }
        }

        $posterdata = Forum::getUserDataFromId($uid);
        $useroutils = '';

        if (($user) and ($uid != 1)) {
            $useroutils .= '<a class=" text-primary me-3" href="powerpack.php?op=instant_message&amp;to_userid=' . $posterdata["uname"] . '" ><i class="far fa-envelope fa-2x" title="' . translate('Envoyer un message interne') . '" data-bs-toggle="tooltip"></i></a>&nbsp;';
        }

        if (array_key_exists('femail', $posterdata)) {
            if ($posterdata['femail'] != '') {
                $useroutils .= '<a class=" text-primary me-3" href="mailto:' . Spam::antiSpam($posterdata['femail'], 1) . '" target="_blank" ><i class="fa fa-at fa-2x" title="' . translate('Email') . '" data-bs-toggle="tooltip"></i></a>&nbsp;';
            }
        }

        if (array_key_exists('url', $posterdata)) {
            if ($posterdata['url'] != '') {
                $useroutils .= '<a class=" text-primary me-3" href="' . $posterdata['url'] . '" target="_blank" ><i class="fas fa-external-link-alt fa-2x" title="' . translate('Visiter ce site web') . '" data-bs-toggle="tooltip"></i></a>&nbsp;';
            }
        }

        if (array_key_exists('mns', $posterdata)) {
            if ($posterdata['mns']) {
                $useroutils .= '<a class=" text-primary me-3" href="minisite.php?op=' . $posterdata['uname'] . '" target="_blank" ><i class="fa fa-desktop fa-2x" title="' . translate('Visitez le minisite') . '" data-bs-toggle="tooltip"></i></a>&nbsp;';
            }
        }

        echo '<div class="d-flex flex-row flex-wrap">
            <div class="me-2 my-auto"><img src="' . $direktori . $user_avatar . '" class=" rounded-circle center-block n-ava-64 align-middle" /></div>
            <div class="align-self-center">
                <h2>' . translate('Utilisateur') . '<span class="d-inline-block text-body-secondary ms-1">' . $uname . '</span></h2>';

        if (isset($cookie[1])) {
            if ($uname !== $cookie[1]) {
                echo $useroutils;
            }
        }

        echo $my_rs;

        if (isset($cookie[1])) {
            if ($uname == $cookie[1]) {
                echo '<p class="lead">' . translate('Si vous souhaitez personnaliser un peu le site, c\'est l\'endroit indiqué. ') . '</p>';
            }
        }

        echo '</div>
        </div>
        <hr />';

        if (isset($cookie[1])) {
            if ($uname == $cookie[1]) {
                User::displayMemberMenu($mns, $uname);
            }
        }

        include 'modules/geoloc/config/config.php';

        echo '<div class="card card-body">
            <div class="row">';

        if (array_key_exists($ch_lat, $posterdata_extend) and array_key_exists($ch_lon, $posterdata_extend))
            if ($posterdata_extend[$ch_lat] != '' and $posterdata_extend[$ch_lon] != '') {
                $C7 = $posterdata_extend[$ch_lat];
                $C8 = $posterdata_extend[$ch_lon];
                echo '<div class="col-md-6">';
            } else {
                echo '<div class="col-md-12">';
            }

        include 'library/sform/extend-user/aff_extend-user.php';

        echo '</div>';

        //==> geoloc
        if (array_key_exists($ch_lat, $posterdata_extend) and array_key_exists($ch_lon, $posterdata_extend)) {
            if ($posterdata_extend[$ch_lat] != '' and $posterdata_extend[$ch_lon] != '') {
                $content = '';

                if (!defined('OL')) {
                    define('OL', 'ol');

                    $content .= '<script type="text/javascript" src="' . $nuke_url . '/assets/shared/ol/ol.js"></script>';
                }

                $content .= '<div class="col-md-6">
                    <div id="map_user" tabindex="300" style="width:100%; height:400px;" lang="' . Language::languageIso(1, 0, 0) . '">
                    <div id="ol_popup"></div>
                    </div>
                    <script type="module">
                    //<![CDATA[
                        if (!$("link[href=\'/assets/shared/ol/ol.css\']").length)
                            $("head link[rel=\'stylesheet\']").last().after("<link rel=\'stylesheet\' href=\'' . $nuke_url . '/assets/shared/ol/ol.css\' type=\'text/css\' media=\'screen\'>");
                        if (!$("link[href=\'/modules/geoloc/include/css/geoloc_style.css\']").length)
                            $("head link[rel=\'stylesheet\']").last().after("<link rel=\'stylesheet\' href=\'' . $nuke_url . '/modules/geoloc/assets/css/geoloc_style.css\' type=\'text/css\' media=\'screen\'>");
                    $(function(){
                    var 
                        iconFeature = new ol.Feature({
                            geometry: new ol.geom.Point(
                            ol.proj.fromLonLat([' . $posterdata_extend[$ch_lon] . ',' . $posterdata_extend[$ch_lat] . '])
                            ),
                            name: "' . $uname . '"
                        }),
                        iconStyle = new ol.style.Style({
                            image: new ol.style.Icon({
                            src: "' . $ch_img . $nm_img_mbcg . '"
                            })
                        });
                    iconFeature.setStyle(iconStyle);
                    var 
                        vectorSource = new ol.source.Vector({features: [iconFeature]}),
                        vectorLayer = new ol.layer.Vector({source: vectorSource}),
                        map = new ol.Map({
                            interactions: new ol.interaction.defaults.defaults({
                                constrainResolution: true, onFocusOnly: true
                            }),
                            target: document.getElementById("map_user"),
                            layers: [
                            new ol.layer.Tile({
                                source: new ol.source.OSM()
                            })
                            ],
                            view: new ol.View({
                            center: ol.proj.fromLonLat([' . $posterdata_extend[$ch_lon] . ', ' . $posterdata_extend[$ch_lat] . ']),
                            zoom: 12
                            })
                        });
                    //Adding a marker on the map
                    map.addLayer(vectorLayer);

                    var element = document.getElementById("ol_popup");
                    var popup = new ol.Overlay({
                        element: element,
                        positioning: "bottom-center",
                        stopEvent: false,
                        offset: [0, -20]
                    });
                    map.addOverlay(popup);

                    // display popup on click
                    map.on("click", function(evt) {
                    var feature = map.forEachFeatureAtPixel(evt.pixel,
                        function(feature) {
                        return feature;
                        });
                    if (feature) {
                        var coordinates = feature.getGeometry().getCoordinates();
                        popup.setPosition(coordinates);
                        $(element).popover({
                        placement: "top",
                        html: true,
                        content: feature.get("name")
                        });
                        $(element).popover("show");
                    } else {
                        $(element).popover("hide");
                    }
                    });
                    // change mouse cursor when over marker
                    map.on("pointermove", function(e) {
                    if (e.dragging) {
                        $(element).popover("hide");
                        return;
                    }
                    var pixel = map.getEventPixel(e.originalEvent);
                    });
                    // Create the graticule component
                    var graticule = new ol.layer.Graticule();
                    graticule.setMap(map);';

                $content .= file_get_contents('modules/geoloc/assets/js/ol-dico.js');

                $content .= '
                    const targ = map.getTarget();
                    const lang = targ.lang;
                    for (var i in dic) {
                        if (dic.hasOwnProperty(i)) {
                            $("#map_user "+dic[i].cla).prop("title", dic[i][lang]);
                        }
                    }
                    $("#map_user .ol-zoom-in, #map_user .ol-zoom-out").tooltip({placement: "right", container: "#map_user",});
                    $("#map_user .ol-rotate-reset, #map_user .ol-attribution button[title]").tooltip({placement: "left", container: "#map_user",});
                    });
                    //]]>
                    </script>';

                $content .= '<div class="mt-3">
                    <a href="modules.php?ModPath=geoloc&amp;ModStart=geoloc"><i class="fa fa-globe fa-lg"></i>&nbsp;[french]Carte[/french][english]Map[/english][chinese]&#x5730;&#x56FE;[/chinese][spanish]Mapa[/spanish][german]Karte[/german]</a>';

                if ($admin) {
                    $content .= '<a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath=geoloc&amp;ModStart=admin/geoloc_set"><i class="fa fa-cogs fa-lg ms-3"></i>&nbsp;[french]Admin[/french][english]Admin[/english][chinese]Admin[/chinese][spanish]Admin[/spanish][german]Admin[/german]</a>';
                }

                $content .= '</div>
                </div>';

                $content = Language::affLangue($content);

                echo $content;
            }
        }

        echo '</div>
        </div>';

        // @deprecated metalang voir component
        if ($uid != 1) {
            echo '<br />
            <h4>' . translate('Journal en ligne de ') . ' ' . $uname . '.</h4>
            <div id="online_user_journal" class="card card-body mb-3">' . Metalang::metaLang($user_journal) . '</div>';
        }

        $file = '';

        $handle = opendir('modules/comments/config');

        while (false !== ($file = readdir($handle))) {
            //if (!preg_match('#\.conf\.php$#i', $file)) {
            if (!preg_match('#\.php$#i', $file)) {
                continue;
            }

            $topic = '#topic#';

            include 'modules/comments/config/' . $file;

            $filelist[$forum] = $url_ret;
        }

        closedir($handle);

        echo '<h4 class="my-3">' . translate('Les derniers commentaires de') . ' ' . $uname . '.</h4>
        <div id="last_comment_by" class="card card-body mb-3">';

        $url = '';

        $result = sql_query("SELECT topic_id, forum_id, post_text, post_time 
                            FROM " . sql_prefix('posts') . " 
                            WHERE forum_id<0 
                            AND poster_id='$uid' 
                            ORDER BY post_time DESC 
                            LIMIT 0, 10");

        while (list($topic_id, $forum_id, $post_text, $post_time) = sql_fetch_row($result)) {
            $url = str_replace('#topic#', $topic_id, $filelist[$forum_id]);

            echo '<p><a href="' . $url . '">' . translate('Posté : ') . Date::formatTimes($post_time, IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT) . '</a></p>';

            $message = Smilies::smilie(stripslashes($post_text));
            $message = Media::affVideoYt($message);
            $message = str_replace('[addsig]', '', $message);

            echo nl2br($message) . '<hr />';
        }

        echo '</div>
        <h4 class="my-3">' . translate('Les derniers articles de') . ' ' . $uname . '.</h4>
        <div id="last_article_by" class="card card-body mb-3">';

        $xtab = News::newsAff('libre', "WHERE informant='$uname' ORDER BY sid DESC LIMIT 10", '', 10);

        $story_limit = 0;

        while (($story_limit < 10) and ($story_limit < sizeof($xtab))) {
            list($sid, $catid, $aid, $title, $time) = $xtab[$story_limit];

            $story_limit++;

            echo '<div class="d-flex border-bottom">
                <div class="p-2"><a href="article.php?sid=' . $sid . '">' . Language::affLangue($title) . '</a></div>
                <div class="ms-auto p-2">' . Date::formatTimes($time, IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM) . '</div>
            </div>';
        }

        echo '</div>
        <h4 class="my-3">' . translate('Les dernières contributions de') . ' ' . $uname . '</h4>
        <div id="last_posts_by" class="card card-body mb-3">';

        $nbp = 10;
        $content = '';

        $result = sql_query("SELECT * 
                            FROM " . sql_prefix('posts') . "
                            WHERE forum_id > 0 
                            AND poster_id=$uid ORDER BY post_time DESC 
                            LIMIT 0, 50");

        $j = 1;

        while (list($post_id, $post_text) = sql_fetch_row($result) and $j <= $nbp) {

            // Requete detail dernier post
            $res = sql_query("SELECT us.topic_id, us.forum_id, us.poster_id, us.post_time, uv.topic_title, ug.forum_name, ug.forum_type, ug.forum_pass, ut.uname 
                            FROM " . sql_prefix('posts') . " us, " . sql_prefix('forumtopics') . " uv, " . sql_prefix('forums') . " ug, " . sql_prefix('users') . " ut 
                            WHERE us.post_id = $post_id 
                            AND uv.topic_id = us.topic_id 
                            AND uv.forum_id = ug.forum_id 
                            AND ut.uid = us.poster_id 
                            LIMIT 1");

            list($topic_id, $forum_id, $poster_id, $post_time, $topic_title, $forum_name, $forum_type, $forum_pass, $uname) = sql_fetch_row($res);

            if (($forum_type == '5') or ($forum_type == '7')) {
                $ok_affich = false;

                $tab_groupe = Groupe::validGroup($user);
                $ok_affich = Groupe::groupeForum($forum_pass, $tab_groupe);
            } else {
                $ok_affich = true;
            }

            if ($ok_affich) {

                // Nbre de postes par sujet
                $TableRep = sql_query("SELECT * 
                                    FROM " . sql_prefix('posts') . " 
                                    WHERE forum_id > 0 
                                    AND topic_id = '$topic_id'");

                $replys = sql_num_rows($TableRep) - 1;

                $id_lecteur = isset($cookie[0]) ? $cookie[0] : '0';

                $sqlR = "SELECT rid 
                        FROM " . sql_prefix('forum_read') . " 
                        WHERE topicid = '$topic_id' 
                        AND uid = '$id_lecteur' 
                        AND status != '0'";

                if (sql_num_rows(sql_query($sqlR)) == 0) {
                    $image = '<a href="" title="' . translate('Non lu') . '" data-bs-toggle="tooltip"><i class="far fa-file-alt fa-lg faa-shake animated text-primary "></i></a>';
                } else {
                    $image = '<a title="' . translate('Lu') . '" data-bs-toggle="tooltip"><i class="far fa-file-alt fa-lg text-primary"></i></a>';
                }

                $content .= '<p class="mb-0 list-group-item list-group-item-action flex-column align-items-start border-bottom pb-1" >
                    <span class="d-flex w-100 mt-1">
                    <span>' . Date::formatTimes($post_time, IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM) . '</span>
                    <span class="ms-auto">
                    <span class="badge bg-secondary ms-1" title="' . translate('Réponses') . '" data-bs-toggle="tooltip" data-bs-placement="left">' . $replys . '</span>
                    </span>
                </span>
                <span class="d-flex w-100"><br /><a href="viewtopic.php?topic=' . $topic_id . '&forum=' . $forum_id . '" data-bs-toggle="tooltip" title="' . $forum_name . '">' . $topic_title . '</a><span class="ms-auto mt-1">' . $image . '</span></span>
                </p>';

                $j++;
            }
        }

        echo $content . '
        </div>
        <hr />';

        if ($posterdata['attachsig'] == 1) {
            echo '<p class="n-signature">' . $user_sig . '</p>';
        }

        //include 'footer.php';
    }

}
