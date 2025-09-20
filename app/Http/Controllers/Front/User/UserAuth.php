<?php

namespace App\Http\Controllers\Front\User;

use App\Support\Facades\Password;
use Npds\Support\Facades\Request;
use App\Library\User\Traits\UserLogout;
use App\Http\Controllers\Core\FrontBaseController;


class UserAuth extends FrontBaseController
{

    use UserLogout;

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        /*
        switch ($op) {

            case 'logout':
                logout();
                break;

            case 'login':
                $this->login($uname, $pass);
                break;
        }
        */

        parent::initialize();
    }

    function login($uname, $pass)
    {
        global $setinfo;

        $result = sql_query("SELECT pass, hashkey, uid, uname, storynum, umode, uorder, thold, noscore, ublockon, theme, commentmax, user_langue 
                            FROM " . sql_prefix('users') . " 
                            WHERE uname = '$uname'");

        if (sql_num_rows($result) == 1) {
            $setinfo = sql_fetch_assoc($result);

            $result = sql_query("SELECT open 
                                FROM " . sql_prefix('users_status') . " 
                                WHERE uid='" . $setinfo['uid'] . "'");

            list($open_user) = sql_fetch_row($result);

            if ($open_user == 0) {
                Header('Location: user.php?stop=99');
                return;
            }

            $dbpass = $setinfo['pass'];

            $pass = (PHP_VERSION_ID >= 80200)
                ? mb_convert_encoding($pass, 'ISO-8859-1', 'UTF-8')
                : utf8_decode($pass);

            if (password_verify($pass, $dbpass) or (strcmp($dbpass, $pass) == 0)) {
                if (!$setinfo['hashkey']) {

                    $AlgoCrypt  = PASSWORD_BCRYPT;
                    $min_ms     = 100;
                    $options    = ['cost' => Password::getOptimalBcryptCostParameter($pass, $AlgoCrypt, $min_ms)];
                    $hashpass   = password_hash($pass, $AlgoCrypt, $options);
                    $pass       = crypt($pass, $hashpass);

                    sql_query("UPDATE " . sql_prefix('users') . " 
                            SET pass='$pass', hashkey='1' 
                            WHERE uname='$uname'");

                    $result = sql_query("SELECT pass, hashkey, uid, uname, storynum, umode, uorder, thold, noscore, ublockon, theme, commentmax, user_langue 
                                        FROM " . sql_prefix('users') . " 
                                        WHERE uname = '$uname'");

                    if (sql_num_rows($result) == 1) {
                        $setinfo = sql_fetch_assoc($result);
                    }

                    $dbpass = $setinfo['pass'];
                    $scryptPass = crypt($dbpass, $hashpass);
                }
            } else {
                $scryptPass = '';
            }

            if (password_verify(urldecode($pass), $dbpass) or password_verify($pass, $dbpass)) {
                $CryptpPWD = $dbpass;
            } elseif (password_verify($dbpass, $scryptPass) or strcmp($dbpass, $pass) == 0) {
                $CryptpPWD = $pass;
            } else {
                Header('Location: user.php?stop=1');
                return;
            }

            Password::docookie($setinfo['uid'], $setinfo['uname'], $CryptpPWD, $setinfo['storynum'], $setinfo['umode'], $setinfo['uorder'], $setinfo['thold'], $setinfo['noscore'], $setinfo['ublockon'], $setinfo['theme'], $setinfo['commentmax'], $setinfo['user_langue']);

            $ip = Request::getip();

            $result = sql_query("SELECT * 
                                FROM " . sql_prefix('session') . " 
                                WHERE host_addr='$ip' 
                                AND guest='1'");

            if (sql_num_rows($result) == 1) {
                sql_query("DELETE FROM " . sql_prefix('session') . " 
                        WHERE host_addr='$ip' 
                        AND guest='1'");
            }

            Header('Location: index.php');
        } else {
            Header('Location: user.php?stop=1');
        }
    }

}
