<?php

namespace App\Library\Stat;


class Stat
{

    #autodoc Site_Load() : Maintient les informations de NB connexion (membre, anonyme) - globalise la variable $who_online_num et maintient le fichier cache/site_load.log &agrave; jour<br />Indispensable pour la gestion de la 'clean_limit' de SuperCache
    function Site_Load()
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

    #autodoc req_stat() : Retourne un tableau contenant les nombres pour les statistiques du site (stats.php)
    function req_stat()
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
