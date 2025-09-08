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


global $powerpack;
$powerpack = true;

settype($op, 'string');

switch ($op) {

    // Purge Chat Box
    case 'admin_chatbox_write':
        if ($admin) {
            $adminX = base64_decode($admin);
            $adminR = explode(':', $adminX);

            $Q = sql_fetch_assoc(sql_query("SELECT * 
                                            FROM " . sql_prefix('authors') . " 
                                            WHERE aid='$adminR[0]' 
                                            LIMIT 1"));

            if ($Q['radminsuper'] == 1)
                if ($chatbox_clearDB == 'OK') {
                    sql_query("DELETE FROM " . sql_prefix('chatbox') . " 
                               WHERE date <= " . (time() - (60 * 5)));
                }
        }

        Header('Location: index.php');
        break;
}
