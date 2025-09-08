<?php

use App\Library\Theme\Theme;
use App\Library\Http\Request;

if (! function_exists('online'))
{ 
    #autodoc online() : Bloc Online (Who_Online) <br />=> syntaxe : function#online
    function online()
    {
        global $user, $cookie;

        $ip = Request::getip();

        $username = isset($cookie[1]) ? $cookie[1] : '';

        if ($username == '') {
            $username = $ip;
            $guest = 1;
        } else {
            $guest = 0;
        }

        $past = time() - 300;

        sql_query("DELETE FROM " . sql_prefix('session') . " WHERE time < '$past'");

        $ctime = time();

        $result = sql_query("SELECT time 
                            FROM " . sql_prefix('session') . " 
                            WHERE username='$username'");

        if (sql_fetch_row($result)) {
            sql_query("UPDATE " . sql_prefix('session') . " 
                    SET username='$username', time='$ctime', host_addr='$ip', guest='$guest' 
                    WHERE username='$username'");

        } else {
            sql_query("INSERT INTO " . sql_prefix('session') . " (username, time, host_addr, guest) VALUES ('$username', '$ctime', '$ip', '$guest')");
        }

        $result = sql_query("SELECT username 
                            FROM " . sql_prefix('session') . " 
                            WHERE guest=1");

        $guest_online_num = sql_num_rows($result);

        $result = sql_query("SELECT username 
                            FROM " . sql_prefix('session') . " 
                            WHERE guest=0");

        $member_online_num = sql_num_rows($result);

        //$who_online_num = $guest_online_num + $member_online_num;

        $who_online = '<p class="text-center">' . translate('Il y a actuellement') . ' <span class="badge bg-secondary">' . $guest_online_num . '</span> ' . translate('visiteur(s) et') . ' <span class="badge bg-secondary">' . $member_online_num . ' </span> ' . translate('membre(s) en ligne.') . '<br />';

        $content = $who_online;

        if ($user) {
            $content .= '<br />' . translate('Vous êtes connecté en tant que') . ' <strong>' . $username . '</strong>.<br />';

            $result = Q_select("SELECT uid 
                                FROM " . sql_prefix('users') . " 
                                WHERE uname='$username'", 86400);

            $uid = $result[0];

            $result2 = sql_query("SELECT to_userid 
                                FROM " . sql_prefix('priv_msgs') . " 
                                WHERE to_userid='" . $uid['uid'] . "' 
                                AND type_msg='0'");
                                
            $numrow = sql_num_rows($result2);

            $content .= translate('Vous avez') . ' <a href="viewpmsg.php"><span class="badge bg-primary">' . $numrow . '</span></a> ' . translate('message(s) personnel(s).') . '</p>
                            ';
        } else {
            $content .= '<br />' . translate('Devenez membre privilégié en cliquant') . ' <a href="user.php?op=only_newuser">' . translate('ici') . '</a></p>';
        }

        global $block_title;
        $title = $block_title == '' ? translate('Qui est en ligne ?') : $block_title;

        Theme::themeSidebox($title, $content);
    }
}