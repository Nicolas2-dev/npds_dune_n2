<?php

if (! function_exists('instant_members_message')) {
    #autodoc:<Powerpack_f.php>
    #autodoc <span class="text-success">BLOCS NPDS</span>:
    #autodoc instant_members_message() : Bloc MI (Message Interne) <br />=> syntaxe : function#instant_members_message
    function instant_members_message()
    {
        global $user, $admin, $long_chain;

        settype($boxstuff, 'string');

        if (!$long_chain) {
            $long_chain = 13;
        }

        global $block_title;
        if ($block_title == '') {
            $block_title = translate('M2M bloc');
        }

        if ($user) {
            global $cookie;

            $boxstuff = '<ul>';

            $ibid = online_members();

            $rank1 = '';

            for ($i = 1; $i <= $ibid[0]; $i++) {

                $timex = time() - $ibid[$i]['time'];

                if ($timex >= 60) {
                    $timex = '<i class="fa fa-plug text-body-secondary" title="' . $ibid[$i]['username'] . ' ' . translate('n\'est pas connecté') . '" data-bs-toggle="tooltip" data-bs-placement="right"></i>&nbsp;';
                } else {
                    $timex = '<i class="fa fa-plug faa-flash animated text-primary" title="' . $ibid[$i]['username'] . ' ' . translate('est connecté') . '" data-bs-toggle="tooltip" data-bs-placement="right" ></i>&nbsp;';
                }

                global $member_invisible;
                if ($member_invisible) {
                    if ($admin) {
                        $and = '';
                    } else {
                        $and = ($ibid[$i]['username'] == $cookie[1]) ? '' : 'AND is_visible=1';
                    }
                } else {
                    $and = '';
                }

                $result = sql_query("SELECT uid 
                                    FROM " . sql_prefix('users') . " 
                                    WHERE uname='" . $ibid[$i]['username'] . "' $and");

                list($userid) = sql_fetch_row($result);

                if ($userid) {
                    $rowQ1 = Q_Select("SELECT rang 
                                    FROM " . sql_prefix('users_status') . " 
                                    WHERE uid='$userid'", 3600);

                    $myrow = $rowQ1[0];

                    $rank = $myrow['rang'];

                    if ($rank) {
                        if ($rank1 == '') {
                            if ($rowQ2 = Q_Select("SELECT rank1, rank2, rank3, rank4, rank5 
                                                FROM " . sql_prefix('config') . "", 86400)) {

                                $myrow = $rowQ2[0];

                                $rank1 = $myrow['rank1'];
                                $rank2 = $myrow['rank2'];
                                $rank3 = $myrow['rank3'];
                                $rank4 = $myrow['rank4'];
                                $rank5 = $myrow['rank5'];
                            }
                        }

                        if ($ibidR = theme_image('forum/rank/' . $rank . '.gif')) {
                            $imgtmpA = $ibidR;
                        } else {
                            $imgtmpA = 'assets/images/forum/rank/' . $rank . '.gif';
                        }

                        $messR = 'rank' . $rank;

                        $tmpR = "<img src=\"" . $imgtmpA . "\" border=\"0\" alt=\"" . aff_langue($$messR) . "\" title=\"" . aff_langue($$messR) . "\" loading=\"lazy\" />";
                    } else {
                        $tmpR = '&nbsp;';
                    }

                    $new_messages = sql_num_rows(sql_query("SELECT msg_id 
                                                            FROM " . sql_prefix('priv_msgs') . " 
                                                            WHERE to_userid = '$userid' 
                                                            AND read_msg='0' 
                                                            AND type_msg='0'"));

                    if ($new_messages > 0) {
                        $PopUp = JavaPopUp('readpmsg_imm.php?op=new_msg', 'IMM', 600, 500);
                        $PopUp = "<a href=\"javascript:void(0);\" onclick=\"window.open($PopUp);\">";

                        $icon = ($ibid[$i]['username'] == $cookie[1]) ? $PopUp : '';
                        $icon .= '<i class="fa fa-envelope fa-lg faa-shake animated" title="' . translate('Nouveau') . '<span class=\'px-2 rounded-pill bg-danger ms-2\'>' . $new_messages . '</span>" data-bs-html="true" data-bs-toggle="tooltip"></i>';

                        if ($ibid[$i]['username'] == $cookie[1]) {
                            $icon .= '</a>';
                        }
                    } else {
                        $messages = sql_num_rows(sql_query("SELECT msg_id 
                                                            FROM " . sql_prefix('priv_msgs') . " 
                                                            WHERE to_userid = '$userid' 
                                                            AND type_msg='0' 
                                                            AND dossier='...'"));

                        if ($messages > 0) {
                            $PopUp = JavaPopUp('readpmsg_imm.php?op=msg', 'IMM', 600, 500);
                            $PopUp = '<a href="javascript:void(0);" onclick="window.open(' . $PopUp . ');">';

                            $icon = ($ibid[$i]['username'] == $cookie[1]) ? $PopUp : '';
                            $icon .= '<i class="far fa-envelope-open fa-lg " title="' . translate('Nouveau') . ' : ' . $new_messages . '" data-bs-toggle="tooltip"></i></a>';
                        } else {
                            $icon = '&nbsp;';
                        }
                    }

                    $N = $ibid[$i]['username'];
                    $M = (strlen($N) > $long_chain) ? substr($N, 0, $long_chain) . '.' : $N;

                    $boxstuff .= '<li class="my-2">' . $timex . '&nbsp;<a href="powerpack.php?op=instant_message&amp;to_userid=' . $N . '" title="' . translate('Envoyer un message interne') . '" data-bs-toggle="tooltip" >' . $M . '</a><span class="float-end">' . $icon . '</span></li>';
                } //suppression temporaire ... rank  '.$tmpR.'
            }

            $boxstuff .= '</ul>';

            themesidebox($block_title, $boxstuff);
        } else {
            if ($admin) {
                $ibid = online_members();

                if ($ibid[0]) {
                    for ($i = 1; $i <= $ibid[0]; $i++) {
                        $N = $ibid[$i]['username'];
                        $M = strlen($N) > $long_chain ? substr($N, 0, $long_chain) . '.' : $N;
                        $boxstuff .= $M . '<br />';
                    }

                    themesidebox('<i>' . $block_title . '</i>', $boxstuff);
                }
            }
        }
    }
}
