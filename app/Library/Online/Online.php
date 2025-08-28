<?php

namespace App\Library\Online;


class Online
{

    #autodoc Who_Online() : Qui est en ligne ? + message de bienvenue
    function Who_Online()
    {
        list($content1, $content2) = Who_Online_Sub();

        return array($content1, $content2);
    }

    #autodoc Who_Online() : Qui est en ligne ? + message de bienvenue / SOUS-Fonction / Utilise Site_Load
    function Who_Online_Sub()
    {
        global $user, $cookie;

        list($member_online_num, $guest_online_num) = site_load();

        $content1 = "$guest_online_num " . translate('visiteur(s) et') . " $member_online_num " . translate('membre(s) en ligne.');

        if ($user) {
            $content2 = translate('Vous êtes connecté en tant que') . ' <b>' . $cookie[1] . '</b>';
        } else {
            $content2 = translate('Devenez membre privilégié en cliquant') . " <a href=\"user.php?op=only_newuser\">" . translate('ici') . "</a>";
        }

        return array($content1, $content2);
    }

    #autodoc online_members () : liste des membres connect&eacute;s <br /> Retourne un tableau dont la position 0 est le nombre, puis la liste des username | time <br />Appel : $xx=online_members(); puis $xx[x]['username'] $xx[x]['time'] ...
    function online_members()
    {
        $result = sql_query("SELECT username, guest, time 
                            FROM " . sql_prefix('session') . " 
                            WHERE guest='0' 
                            ORDER BY username ASC");

        $i = 0;

        $members_online[$i] = sql_num_rows($result);

        while ($session = sql_fetch_assoc($result)) {
            if (isset($session['guest']) and $session['guest'] == 0) {
                $i++;

                $members_online[$i]['username'] = $session['username'];
                $members_online[$i]['time'] = $session['time'];
            }
        }

        return $members_online;
    }

}
