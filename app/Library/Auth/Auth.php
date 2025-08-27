<?php

namespace App\Library\auth;


class Auth
{

    #autodoc getusrinfo($user) : Renvoi le contenu de la table users pour le user uname
    function getusrinfo($user)
    {
        $cookie = explode(':', base64_decode($user));

        $result = sql_query("SELECT pass 
                            FROM " . sql_prefix('users') . " 
                            WHERE uname='$cookie[1]'");

        list($pass) = sql_fetch_row($result);

        $userinfo = '';

        if (($cookie[2] == md5($pass)) and ($pass != '')) {
            $result = sql_query("SELECT uid, name, uname, email, femail, url, user_avatar, user_occ, user_from, user_intrest, user_sig, user_viewemail, user_theme, pass, storynum, umode, uorder, thold, noscore, bio, ublockon, ublock, theme, commentmax, user_journal, send_email, is_visible, mns, user_lnl 
                                FROM " . sql_prefix('users') . " 
                                WHERE uname='$cookie[1]'");

            if (sql_num_rows($result) == 1) {
                $userinfo = sql_fetch_assoc($result);
            } else {
                echo '<strong>' . translate('Un problème est survenu') . '.</strong>';
            }
        }

        return $userinfo;
    }

    #autodoc AutoReg() : Si AutoRegUser=true et que le user ne dispose pas du droit de connexion : RAZ du cookie NPDS<br />retourne False ou True
    function AutoReg()
    {
        global $AutoRegUser, $user;

        if (!$AutoRegUser) {
            if (isset($user)) {

                $cookie = explode(':', base64_decode($user));

                list($test) = sql_fetch_row(sql_query("SELECT open 
                                                    FROM " . sql_prefix('users_status') . " 
                                                    WHERE uid='$cookie[0]'"));

                if (!$test) {
                    setcookie('user', '', 0);

                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    #autodoc autorisation($auto) : Retourne true ou false en fonction des paramètres d'autorisation de NPDS (Administrateur, anonyme, Membre, Groupe de Membre, Tous)
    function autorisation($auto)
    {
        global $user, $admin;

        $affich = false;

        if (($auto == -1) and (!$user)) {
            $affich = true;
        }

        if (($auto == 1) and (isset($user))) {
            $affich = true;
        }

        if ($auto > 1) {
            $tab_groupe = valid_group($user);

            if ($tab_groupe) {
                foreach ($tab_groupe as $groupevalue) {
                    if ($groupevalue == $auto) {
                        $affich = true;
                        break;
                    }
                }
            }
        }

        if ($auto == 0) {
            $affich = true;
        }

        if (($auto == -127) and ($admin)) {
            $affich = true;
        }

        return $affich;
    }

    #autodoc secur_static($sec_type) : Pour savoir si le visiteur est un : membre ou admin (static.php et banners.php par exemple)
    function secur_static($sec_type)
    {
        global $user, $admin;

        switch ($sec_type) {

            case 'member':
                return isset($user);
                break;

            case 'admin':
                return isset($admin);
                break;
        }
    }

}
