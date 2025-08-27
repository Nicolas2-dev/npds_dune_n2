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

}
