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

use App\Library\String\Sanitize;
use App\Library\Messenger\Messenger;

global $powerpack;
$powerpack = true;

settype($op, 'string');

switch ($op) {

    // Instant Members Message
    case 'instant_message':
        Messenger::FormInstantMessage($to_userid);
        break;

    case 'write_instant_message':
        settype($copie, 'string');
        settype($messages, 'string');

        if (isset($user)) {
            $rowQ1 = Q_Select("SELECT uid 
                               FROM " . sql_prefix('users') . " 
                               WHERE uname='$cookie[1]'", 3600);

            $uid = $rowQ1[0];

            $from_userid = $uid['uid'];

            if (($subject != '') or ($message != '')) {
                $subject = Sanitize::fixQuotes($subject) . '';
                $messages = Sanitize::fixQuotes($messages) . '';

                Messenger::dbWritePrivateMessage($to_userid, '', $subject, $from_userid, $message, $copie);
            }
        }

        Header('Location: index.php');
        break;

}
