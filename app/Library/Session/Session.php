<?php

namespace App\Library\Session;


class Session
{

    #autodoc session_manage() : Mise à jour la table session
    function session_manage()
    {
        global $cookie, $REQUEST_URI, $nuke_url;

        $guest = 0;

        $ip = getip();

        $username = isset($cookie[1]) ? $cookie[1] : $ip;

        if ($username == $ip) {
            $guest = 1;
        }

        //==> geoloc
        include 'modules/geoloc/config/config.php';

        if ($geo_ip == 1) {
            include 'modules/geoloc/support/geoloc_refip.php';
        }

        //<== geoloc
        $past = time() - 300;

        sql_query("DELETE FROM " . sql_prefix('session') . " 
                WHERE time < '$past'");

        // proto en test badbotcontrol ...
        // bad robot limited at x connections ...
        $gulty_robots = array(
            'facebookexternalhit', 
            'Amazonbot', 
            'ClaudeBot', 
            'bingbot', 
            'Applebot', 
            'AhrefsBot', 
            'SemrushBot'
        ); // to be defined in config.php ...

        foreach ($gulty_robots as $robot) {
            if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], $robot) !== false) {

                $result = sql_query("SELECT agent 
                                    FROM " . sql_prefix('session') . " 
                                    WHERE agent REGEXP '" . $robot . "'");

                if (sql_num_rows($result) > 5) {
                    header($_SERVER["SERVER_PROTOCOL"] . ' 429 Too Many Requests');

                    echo 'Too Many Requests';
                    die;
                }
            }
        }
        // proto

        $result = sql_query("SELECT time 
                            FROM " . sql_prefix('session') . " 
                            WHERE username='$username'");

        if ($row = sql_fetch_assoc($result)) {
            if ($row['time'] < (time() - 30)) {

                sql_query("UPDATE " . sql_prefix('session') . " 
                        SET username='$username', time='" . time() . "', host_addr='$ip', guest='$guest', uri='$REQUEST_URI', agent='" . getenv('HTTP_USER_AGENT') . "' 
                        WHERE username='$username'");

                if ($guest == 0) {
                    global $gmt;
                    sql_query("UPDATE " . sql_prefix('users') . " 
                            SET user_lastvisit='" . (time() + (int)$gmt * 3600) . "' 
                            WHERE uname='$username'");
                }
            }
        } else {
            sql_query("INSERT INTO " . sql_prefix('session') . " (username, time, host_addr, guest, uri, agent) 
                    VALUES ('$username', '" . time() . "', '$ip', '$guest', '$REQUEST_URI', '" . getenv('HTTP_USER_AGENT') . "')");
        }
    }

}