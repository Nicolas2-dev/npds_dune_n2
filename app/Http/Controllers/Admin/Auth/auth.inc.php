<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2025 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

use App\Library\Password\Password;

// controller auth admin !
if ((isset($aid)) and (isset($pwd)) and ($op == 'login')) {
    if ($aid != '' and $pwd != '') {

        $result = sql_query("SELECT pwd, hashkey 
                             FROM " . sql_prefix('authors') . " 
                             WHERE aid='$aid'");

        if (sql_num_rows($result) == 1) {

            $setinfo = sql_fetch_assoc($result);

            $dbpass = $setinfo['pwd'];

            // ne sert a rien !
            //$pwd = (PHP_VERSION_ID >= 80200)
            //    ? mb_convert_encoding($pwd, 'ISO-8859-1', 'UTF-8')
            //    : utf8_decode($pwd);

            // compatible avec PHP 7.x, 8.0, 8.2 et 8.4+
            $pwd = mb_convert_encoding($pwd, 'ISO-8859-1', 'UTF-8');


            $scryptPass = null;

            if (password_verify($pwd, $dbpass) or (strcmp($dbpass, $pwd) == 0)) {
                if (!$setinfo['hashkey']) {

                    $AlgoCrypt  = PASSWORD_BCRYPT;
                    $min_ms     = 100;

                    $options    = [
                        'cost' => Password::getOptimalBcryptCostParameter($pwd, $AlgoCrypt, $min_ms)
                    ];

                    $hashpass   = password_hash($pwd, $AlgoCrypt, $options);

                    $pwd = crypt($pwd, $hashpass);

                    sql_query("UPDATE " . sql_prefix('authors') . " 
                               SET pwd='$pwd', hashkey='1' 
                               WHERE aid='$aid'");

                    $result = sql_query("SELECT pwd, hashkey 
                                         FROM " . sql_prefix('authors') . " 
                                         WHERE aid = '$aid'");

                    if (sql_num_rows($result) == 1) {
                        $setinfo = sql_fetch_assoc($result);
                    }

                    $dbpass = $setinfo['pwd'];
                    $scryptPass = crypt($dbpass, $hashpass);
                }
            }

            if (password_verify($pwd, $dbpass)) {
                $CryptpPWD = $dbpass;
            } elseif (password_verify($dbpass, $scryptPass) or strcmp($dbpass, $pwd) == 0) {
                $CryptpPWD = $pwd;
            } else {
                Admin_Alert(sprintf('Passwd not in DB#1 : ', $aid));
            }

            $admin = base64_encode("$aid:" . md5($CryptpPWD));

            if ($admin_cook_duration <= 0) {
                $admin_cook_duration = 1;
            }

            $timeX = time() + (3600 * $admin_cook_duration);

            setcookie('admin', $admin, $timeX);
            setcookie('adm_exp', $timeX, $timeX);
        }
    }
}

#autodoc $admintest - $super_admintest : permet de savoir si un admin est connect&ecute; ($admintest=true) et s'il est SuperAdmin ($super_admintest=true)
$admintest = false;
$super_admintest = false;

if (isset($admin) and ($admin != '')) {
    $Xadmin = base64_decode($admin);
    $Xadmin = explode(':', $Xadmin);

    $aid = urlencode($Xadmin[0]);

    $AIpwd = $Xadmin[1];

    if ($aid == '' or $AIpwd == '') {
        Admin_Alert('Null Aid or Passwd');
    }

    $result = sql_query("SELECT pwd, radminsuper 
                         FROM " . sql_prefix('authors') . " 
                         WHERE aid = '$aid'");

    if (!$result) {
        Admin_Alert(sprintf('DB not ready #2 : %s / %s', $aid, $AIpwd));
    } else {
        list($AIpass, $Xsuper_admintest) = sql_fetch_row($result);

        if (md5($AIpass) == $AIpwd and $AIpass != '') {
            $admintest = true;
            $super_admintest = $Xsuper_admintest;
        } else {
            Admin_Alert(printf('Password in Cookies not Good #1 : %s / %s', $aid, $AIpwd));
        }
    }

    unset($AIpass);
    unset($AIpwd);
    unset($Xadmin);
    unset($Xsuper_admintest);
}
