<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

if (!function_exists('Mysql_Connexion')) {
    include 'mainfile.php';
}

settype($op, 'string');

if ($op != 'maj_subscribe') {

    include 'header.php';

    $inclusion = false;

    if (file_exists('themes/' . $theme . '/views/partials/news/topics.php')) {
        $inclusion = 'themes/' . $theme . '/views/partials/news/topics.php';
    } elseif (file_exists('themes/base/views/partials/news/topics.php')) {
        $inclusion = 'themes/base/views/partials/news/topics.php';
    } else {
        echo 'views/partials/news/topics.html / not find !<br />';
    }

    if ($inclusion) {
        ob_start();
        include($inclusion);

        $Xcontent = ob_get_contents();
        ob_end_clean();
        echo Metalang::metaLang(Language::affLangue($Xcontent));
    }

    include 'footer.php';
} else {
    if ($subscribe) {
        if ($user) {
            $result = sql_query("DELETE FROM " . sql_prefix('subscribe') . " 
                                 WHERE uid='$cookie[0]' 
                                 AND topicid IS NOT NULL");

            $selection = sql_query("SELECT topicid 
                                    FROM " . sql_prefix('topics') . " 
                                    ORDER BY topicid");

            while (list($topicid) = sql_fetch_row($selection)) {
                if (isset($Subtopicid)) {
                    if (array_key_exists($topicid, $Subtopicid)) {
                        if ($Subtopicid[$topicid] == "on") {
                            $resultX = sql_query("INSERT INTO " . sql_prefix('subscribe') . " (topicid, uid) 
                                                  VALUES ('$topicid','$cookie[0]')");
                        }
                    }
                }
            }

            redirectUrl('topics.php');
        }
    }
}
