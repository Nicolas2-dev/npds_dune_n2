<?php

namespace App\Http\Controllers\Admin\Auth;


use Npds\Config\Config;
use App\Support\Facades\Access;
use App\Support\Facades\Password;
use App\Support\Facades\Validation;
use App\Http\Controllers\Core\AdminBaseController;


class Auth extends AdminBaseController
{


    protected int $pdst = 0;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        /*
        case 'logout':
            setcookie('admin');
            setcookie('adm_exp');

            unset($admin);

            Header('Location: index.php');
            break;

            // tout ce bordel va être deprecated !
            if ($admintest) {

            } else {
                login();
            }
        */

        parent::initialize();        
    }

    public function login()
    {
        Validation::adminFoot('fv', '', 'var formulid =["adminlogin"];', '');

        return $this->createView()
            ->shares('title', 'Administration Connexion');

    }

    public function loginSubmit() 
    {
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
                        Access::AdminAlert(sprintf('Passwd not in DB#1 : ', $aid));
                    }

                    $admin = base64_encode("$aid:" . md5($CryptpPWD));

                    $timeX = time() + (3600 * Config::get('cookie.admin_cook_duration', 1));

                    setcookie('admin', $admin, $timeX);
                    setcookie('adm_exp', $timeX, $timeX);
                }
            }
        }
    }
    
}
