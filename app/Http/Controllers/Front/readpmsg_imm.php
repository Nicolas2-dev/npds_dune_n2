<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

if (!function_exists('Mysql_Connexion')) {
    include 'mainfile.php';
}

include 'functions.php';

$cache_obj = $SuperCache ? new SuperCacheManager() : new SuperCacheEmpty();

include 'auth.php';

function cache_ctrl()
{
    global $cache_verif;

    if ($cache_verif) {
        header('Expires: Sun, 01 Jul 1990 00:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-cache, must revalidate');
        header('Pragma: no-cache');
    }
}

function show_imm($op)
{
    global $smilies, $user, $allow_bbcode, $language, $Default_Theme, $Default_Skin, $theme, $short_user, $Titlesitename;

    if (!$user) {
        Header('Location: user.php');
    } else {
        $userX = base64_decode($user);
        $userdata = explode(':', $userX);

        if ($userdata[9] != '') {
            $ibix = explode('+', urldecode($userdata[9]));

            if (array_key_exists(0, $ibix)) {
                $theme = $ibix[0];
            } else {
                $theme = $Default_Theme;
            }

            if (array_key_exists(1, $ibix)) {
                $skin = $ibix[1];
            } else {
                $skin = $Default_Skin;
            }

            $tmp_theme = $theme;

            if (!$file = @opendir('themes/' . $theme)) {
                $tmp_theme = $Default_Theme;
            }
        } else {
            $tmp_theme = $Default_Theme;
        }

        $skin = $skin == '' ? 'default' : $skin;

        include 'themes/' . $theme . '/views/theme.php';

        $userdata = Forum::getUserData($userdata[1]);

        $sql = ($op != 'new_msg')
            ? "SELECT * FROM " . sql_prefix('priv_msgs') . " 
               WHERE to_userid = '" . $userdata['uid'] . "' 
               AND read_msg='1' 
               AND type_msg='0' 
               AND dossier='...' 
               ORDER BY msg_id DESC"

            : "SELECT * FROM " . sql_prefix('priv_msgs') . " 
               WHERE to_userid = '" . $userdata['uid'] . "' 
               AND read_msg='0' 
               AND type_msg='0' 
               ORDER BY msg_id ASC";

        $result = sql_query($sql);

        $pasfin = false;

        while ($myrow = sql_fetch_assoc($result)) {
            if ($pasfin == false) {

                $pasfin = true;

                cache_ctrl();

                include 'storage/meta/meta.php';
                include 'themes/base/bootstrap/header_head.php';

                echo Css::importCss($tmp_theme, $language, $skin, '', '');

                echo '</head>
                <body>
                    <div class="p-3">';
            }

            $posterdata = Forum::getUserDataFromId($myrow['from_userid']);

            echo '<div class="card mb-3">
               <div class="card-body">
                  <div>' . userpopover($posterdata['uname'], 40, 2) . ' <span class="float-end small">' . translate('Envoyé') . ' : ' . $myrow['msg_time'] . '</span>';

            echo '</div>
               <h3>' . translate('Message personnel') . ' ' . translate('de');

            if ($posterdata['uid'] == 1) {
                global $sitename;
                echo ' <span class="text-body-secondary">' . $sitename . '</span></h3>';
            }

            if ($posterdata['uid'] <> 1) {
                echo ' <span class="text-body-secondary">' . $posterdata['uname'] . '</span></h3>';
            }

            $myrow['subject'] = strip_tags($myrow['subject']);

            /*
            $posts = $posterdata['posts'];
            if ($posterdata['uid'] <> 1) {
                echo Forum::memberQualif($posterdata['uname'], $posts, $posterdata['rang']);
            }
            */

            echo '<hr />';

            echo '<h4>' . Language::affLangue($myrow['subject']);

            if ($smilies) {
                if ($myrow['msg_image'] != '') {
                    if ($ibid = themeImage('forum/subject/' . $myrow['msg_image'])) {
                        $imgtmp = $ibid;
                    } else {
                        $imgtmp = 'assets/images/forum/subject/' . $myrow['msg_image'];
                    }

                    echo '<img class="n-smil float-end" src="' . $imgtmp . '" alt="icon-message" />';
                }
            }

            echo '</h4>';

            $message = stripslashes($myrow['msg_text']);

            if ($allow_bbcode) {
                $message = Smilies::smilie($message);
                $message = MediaPlayer::affVideoYt($message);
            }

            $message = str_replace('[addsig]', '<div class="n-signature">' . nl2br($posterdata['user_sig']) . '</div>', Language::affLangue($message)); // ne sert à rien ici ????

            echo $message . '<br />';

            // on se demande a quoi cela peut servir ce code, a rien !!!!
            //if ($posterdata['uid'] <> 1) {
            //    if (!$short_user) {
            //    }
            //}

            echo '</div>
            <div class="card-footer">';

            if ($posterdata['uid'] <> 1) {
                echo '<a class="me-3" href="readpmsg_imm.php?op=read_msg&amp;msg_id=' . $myrow['msg_id'] . '&amp;op_orig=' . $op . '&amp;sub_op=reply" title="' . translate('Répondre') . '" data-bs-toggle="tooltip"><i class="fa fa-reply fa-lg me-1"></i>' . translate('Répondre') . '</a>';
            }

            echo '<a class="me-3" href="readpmsg_imm.php?op=read_msg&amp;msg_id=' . $myrow['msg_id'] . '&amp;op_orig=' . $op . '&amp;sub_op=read" title="' . translate('Lu') . '" data-bs-toggle="tooltip"><i class="far fa-check-square fa-lg"></i></a>
                <a class="me-3 float-end" href="readpmsg_imm.php?op=delete&amp;msg_id=' . $myrow['msg_id'] . '&amp;op_orig=' . $op . '" title="' . translate('Effacer') . '" data-bs-toggle="tooltip" data-bs-placement="left"><i class="fas fa-trash fa-lg text-danger"></i></a>
            </div>
            </div>';
        }

        if ($pasfin != true) {
            cache_ctrl();
            echo '<body onload="self.close();">';
        }
    }

    echo '</div>
            <script type="text/javascript" src="assets/shared/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
            <script type="text/javascript" src="assets/js/npds_adapt.js"></script>
        </body>
    </html>';
}

function sup_imm($msg_id)
{
    global $cookie;

    if (!$cookie) {
        Header('Location: user.php');
    } else {
        $sql = "DELETE FROM " . sql_prefix('priv_msgs') . " 
                WHERE msg_id='$msg_id' 
                AND to_userid='$cookie[0]'";

        if (!sql_query($sql)) {
            Error::forumError('0021');
        }
    }
}

function read_imm($msg_id, $sub_op)
{
    global $cookie;

    if (!$cookie) {
        Header('Location: user.php');
    } else {
        $sql = "UPDATE " . sql_prefix('priv_msgs') . " 
                SET read_msg='1' 
                WHERE msg_id='$msg_id' 
                AND to_userid='$cookie[0]'";

        if (!sql_query($sql)) {
            Error::forumError('0021');
        }

        if ($sub_op == 'reply') {
            echo "<script type=\"text/javascript\">
               //<![CDATA[
                window.location='replypmsg.php?reply=1&msg_id=$msg_id&userid=$cookie[0]&full_interface=short';
               //]]>
               </script>";

            die();
        }

        echo '<script type="text/javascript">
            //<![CDATA[
                window.location="readpmsg_imm.php?op=new_msg";
            //]]>
            </script>';

        die();
    }
}

settype($op, 'string');

switch ($op) {

    case 'new_msg':
        show_imm($op);
        break;

    case 'read_msg':
        read_imm($msg_id, $sub_op);
        break;

    case 'delete':
        sup_imm($msg_id);
        show_imm($op_orig);
        break;

    default:
        show_imm($op);
        break;
}
