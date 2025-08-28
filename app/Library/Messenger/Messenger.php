<?php

namespace App\Library\Messenger;


class Messenger
{

    #autodoc Mess_Check_Mail($username) : Appel la fonction d'affichage du groupe check_mail (theme principal de NPDS) sans class
    function Mess_Check_Mail($username)
    {
        Mess_Check_Mail_interface($username, '');
    }

    #autodoc Mess_Check_Mail_interface($username, $class) : Affiche le groupe check_mail (theme principal de NPDS)
    function Mess_Check_Mail_interface($username, $class)
    {
        global $anonymous;

        if ($ibid = theme_image('fle_b.gif')) {
            $imgtmp = $ibid;
        } else {
            $imgtmp = false;
        }

        if ($class != '') {
            $class = "class=\"$class\"";
        }

        if ($username == $anonymous) {
            if ($imgtmp) {
                echo "<img alt=\"\" src=\"$imgtmp\" align=\"center\" />$username - <a href=\"user.php\" $class>" . translate('Votre compte') . "</a>";
            } else {
                echo "[$username - <a href=\"user.php\" $class>" . translate('Votre compte') . "</a>]";
            }
        } else {
            if ($imgtmp) {
                echo "<a href=\"user.php\" $class><img alt=\"\" src=\"$imgtmp\" align=\"center\" />" . translate('Votre compte') . "</a>&nbsp;" . Mess_Check_Mail_Sub($username, $class);
            } else {
                echo "[<a href=\"user.php\" $class>" . translate('Votre compte') . "</a>&nbsp;&middot;&nbsp;" . Mess_Check_Mail_Sub($username, $class) . "]";
            }
        }
    }

    #autodoc Mess_Check_Mail_Sub($username, $class) : Affiche le groupe check_mail (theme principal de NPDS) / SOUS-Fonction
    function Mess_Check_Mail_Sub($username, $class)
    {
        global $user;

        if ($username) {
            $userdata = explode(':', base64_decode($user));

            $total_messages = sql_num_rows(sql_query("SELECT msg_id 
                                                    FROM " . sql_prefix('priv_msgs') . " 
                                                    WHERE to_userid = '$userdata[0]' 
                                                    AND type_msg='0'"));

            $new_messages = sql_num_rows(sql_query("SELECT msg_id 
                                                    FROM " . sql_prefix('priv_msgs') . " 
                                                    WHERE to_userid = '$userdata[0]' 
                                                    AND read_msg='0' 
                                                    AND type_msg='0'"));

            if ($total_messages > 0) {
                if ($new_messages > 0) {
                    $Xcheck_Nmail = $new_messages;
                } else {
                    $Xcheck_Nmail = '0';
                }

                $Xcheck_mail = $total_messages;
            } else {
                $Xcheck_Nmail = '0';
                $Xcheck_mail = '0';
            }
        }

        $YNmail = "$Xcheck_Nmail";
        $Ymail = "$Xcheck_mail";

        $Mel = "<a href=\"viewpmsg.php\" $class>Mel</a>";

        if ($Xcheck_Nmail > 0) {
            $YNmail = "<a href=\"viewpmsg.php\" $class>$Xcheck_Nmail</a>";
            $Mel = 'Mel';
        }

        if ($Xcheck_mail > 0) {
            $Ymail = "<a href=\"viewpmsg.php\" $class>$Xcheck_mail</a>";
            $Mel = 'Mel';
        }

        return ("$Mel : $YNmail / $Ymail");
    }

    #autodoc Form_instant_message($to_userid) : Ouvre la page d'envoi d'un MI (Message Interne)
    function Form_instant_message($to_userid)
    {
        include 'header.php';

        write_short_private_message(removeHack($to_userid));

        include 'footer.php';
    }

    #autodoc writeDB_private_message($to_userid,$image,$subject,$from_userid,$message, $copie) : Insère un MI dans la base et le cas échéant envoi un mail
    function writeDB_private_message($to_userid, $image, $subject, $from_userid, $message, $copie)
    {
        $res = sql_query("SELECT uid, user_langue 
                        FROM " . sql_prefix('users') . " 
                        WHERE uname='$to_userid'");

        list($to_useridx, $user_languex) = sql_fetch_row($res);

        if ($to_useridx == '') {
            forumerror('0016');
        } else {
            $time = getPartOfTime(time(), 'yyyy-MM-dd H:mm:ss');

            include_once 'language/lang-multi.php';

            $subject = removeHack($subject);

            $message = str_replace("\n", "<br />", $message);
            $message = addslashes(removeHack($message));

            $sql = "INSERT INTO " . sql_prefix('priv_msgs') . " (msg_image, subject, from_userid, to_userid, msg_time, msg_text) 
                    VALUES ('$image', '$subject', '$from_userid', '$to_useridx', '$time', '$message')";

            if (!$result = sql_query($sql)) {
                forumerror('0020');
            }

            if ($copie) {
                $sql = "INSERT INTO " . sql_prefix('priv_msgs') . " (msg_image, subject, from_userid, to_userid, msg_time, msg_text, type_msg, read_msg)
                        VALUES ('$image', '$subject', '$from_userid', '$to_useridx', '$time', '$message', '1', '1')";

                if (!$result = sql_query($sql)) {
                    forumerror('0020');
                }
            }

            global $subscribe, $nuke_url, $sitename;
            if ($subscribe) {
                $sujet = html_entity_decode(translate_ml($user_languex, 'Notification message privé.'), ENT_COMPAT | ENT_HTML401, 'UTF-8') . '[' . $from_userid . '] / ' . $sitename;

                $message = $time . '<br />' . translate_ml($user_languex, 'Bonjour') . '<br />' . translate_ml($user_languex, 'Vous avez un nouveau message.') . '<br /><br /><b>' . $subject . '</b><br /><br /><a href="' . $nuke_url . '/viewpmsg.php">' . translate_ml($user_languex, 'Cliquez ici pour lire votre nouveau message.') . '</a><br />';

                include 'config/signat.php';

                copy_to_email($to_useridx, $sujet, stripslashes($message));
            }
        }
    }

    #autodoc write_short_private_message($to_userid) : Formulaire d'écriture d'un MI
    function write_short_private_message($to_userid)
    {
        echo '<h2>' . translate('Message à un membre') . '</h2>
        <h3><i class="fa fa-at me-1"></i>' . $to_userid . '</h3>
        <form id="sh_priv_mess" action="powerpack.php" method="post">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="subject" >' . translate('Sujet') . '</label>
                <div class="col-sm-12">
                    <input class="form-control" type="text" id="subject" name="subject" maxlength="100" />
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="message" >' . translate('Message') . '</label>
                <div class="col-sm-12">
                    <textarea class="form-control"  id="message" name="message" rows="10"></textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <div class="form-check" >
                    <input class="form-check-input" type="checkbox" id="copie" name="copie" />
                    <label class="form-check-label" for="copie">' . translate('Conserver une copie') . '</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <input type="hidden" name="to_userid" value="' . $to_userid . '" />
                <input type="hidden" name="op" value="write_instant_message" />
                <div class="col-sm-12">
                    <input class="btn btn-primary" type="submit" name="submit" value="' . translate('Valider') . '" accesskey="s" />&nbsp;
                    <button class="btn btn-secondary" type="reset">' . translate('Annuler') . '</button>
                </div>
            </div>
        </form>';
    }
}
