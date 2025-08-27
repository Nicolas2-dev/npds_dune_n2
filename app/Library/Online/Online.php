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

}
