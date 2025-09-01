<?php

namespace App\Library\Online;


class Online
{

    /**
     * Affiche qui est en ligne et un message de bienvenue.
     *
     * @return array{0:string,1:string} Tableau contenant :
     *                                  [0] : message visiteurs/membres en ligne
     *                                  [1] : message de bienvenue pour l'utilisateur connecté ou invité
     */
    public static function whoOnline(): array
    {
        list($content1, $content2) = static::whoOnlineSub();

        return array($content1, $content2);
    }

    /**
     * Sous-fonction de whoOnline() utilisant Site_Load pour récupérer le nombre de membres et invités en ligne.
     *
     * @global array $cookie Tableau des cookies utilisateur
     * @global mixed $user Indique si l'utilisateur est connecté
     * @return array{0:string,1:string} Tableau contenant :
     *                                  [0] : message visiteurs/membres en ligne
     *                                  [1] : message de bienvenue pour l'utilisateur connecté ou invité
     */
    public static function whoOnlineSub(): array
    {
        global $user, $cookie;

        list($member_online_num, $guest_online_num) = static::siteLoad();

        $content1 = "$guest_online_num " . translate('visiteur(s) et') . " $member_online_num " . translate('membre(s) en ligne.');

        if ($user) {
            $content2 = translate('Vous êtes connecté en tant que') . ' <b>' . $cookie[1] . '</b>';
        } else {
            $content2 = translate('Devenez membre privilégié en cliquant') . " <a href=\"user.php?op=only_newuser\">" . translate('ici') . "</a>";
        }

        return array($content1, $content2);
    }

    /**
     * Maintient les informations de nombre de connexions (membres, invités) et met à jour le fichier cache `site_load.log`.
     *
     * Indispensable pour la gestion de la 'clean_limit' de SuperCache.
     *
     * @global int $who_online_num Nombre total d'utilisateurs en ligne
     * @global bool $SuperCache Indique si SuperCache est activé
     * @return array{0:int,1:int} Tableau contenant :
     *                            [0] : nombre de membres en ligne
     *                            [1] : nombre d'invités en ligne
     */
    public static function siteLoad(): array
    {
        global $SuperCache, $who_online_num;

        $guest_online_num = 0;
        $member_online_num = 0;

        $result = sql_query("SELECT COUNT(username) AS TheCount, guest 
                            FROM " . sql_prefix('session') . " 
                            GROUP BY guest");

        while ($TheResult = sql_fetch_assoc($result)) {
            if ($TheResult['guest'] == 0) {
                $member_online_num = $TheResult['TheCount'];
            } else {
                $guest_online_num = $TheResult['TheCount'];
            }
        }

        $who_online_num = $guest_online_num + $member_online_num;

        if ($SuperCache) {
            $file = fopen('storage/logs/site_load.log', 'w');

            fwrite($file, $who_online_num);
            fclose($file);
        }

        return array($member_online_num, $guest_online_num);
    }

    /**
     * Liste les membres connectés.
     *
     * Retourne un tableau dont la position 0 est le nombre de membres en ligne,
     * puis chaque position suivante contient un tableau associatif avec :
     * - 'username' : le nom du membre
     * - 'time'     : la dernière activité
     *
     * @return array<int, mixed> Tableau contenant le nombre de membres et la liste des membres connectés
     */
    public static function onlineMembers(): array
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
