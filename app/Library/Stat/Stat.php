<?php

namespace App\Library\Stat;


class Stat
{

    /**
     * Maintient les informations de nombre de connexions (membres, anonymes)
     * et met à jour le fichier cache `site_load.log`.
     * Indispensable pour la gestion de la 'clean_limit' de SuperCache.
     *
     * @global int $who_online_num
     * @global bool $SuperCache
     * @return array{0:int,1:int} Tableau contenant le nombre de membres en ligne [0] et le nombre d'invités en ligne [1]
     */
    public static function Site_Load(): array
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
     * Retourne un tableau contenant les statistiques du site (membres, news, critiques, forums, sujets, pages vues).
     *
     * @return array<int> Tableau contenant les statistiques dans l'ordre suivant :
     *  [0] => nombre de membres
     *  [1] => nombre de news
     *  [2] => nombre de critiques
     *  [3] => nombre de forums
     *  [4] => nombre de sujets
     *  [5] => nombre total de pages vues
     */
    public static function req_stat(): array
    {
        // Les membres
        $result = sql_query("SELECT uid 
                            FROM " . sql_prefix('users'));

        $xtab[0] = $result ? (sql_num_rows($result) - 1) : '0';

        if ($result) {
            sql_free_result($result);
        }

        // Les Nouvelles (News)
        $result = sql_query("SELECT sid FROM " . sql_prefix('stories'));
        $xtab[1] = $result ? sql_num_rows($result) : '0';

        if ($result) {
            sql_free_result($result);
        }

        // Les Critiques (Reviews))
        $result = sql_query("SELECT id 
                            FROM " . sql_prefix('reviews'));

        $xtab[2] = $result ? sql_num_rows($result) : '0';

        if ($result) {
            sql_free_result($result);
        }

        // Les Forums
        $result = sql_query("SELECT forum_id 
                            FROM " . sql_prefix('forums'));

        $xtab[3] = $result ? sql_num_rows($result) : '0';

        if ($result) {
            sql_free_result($result);
        }

        // Les Sujets (topics)
        $result = sql_query("SELECT topicid 
                            FROM " . sql_prefix('topics'));

        $xtab[4] = $result ? sql_num_rows($result) : '0';

        if ($result) {
            sql_free_result($result);
        }

        // Nombre de pages vues
        $result = sql_query("SELECT count 
                            FROM " . sql_prefix('counter') . " 
                            WHERE type='total'");
        list($totalz) = sql_fetch_row($result);

        $xtab[5] = $totalz++;

        sql_free_result($result);

        return $xtab;
    }

}
