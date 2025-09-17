<?php

namespace App\Http\Controllers\Admin\Auth;


use App\Http\Controllers\Core\AdminBaseController;


class Auth extends AdminBaseController
{

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

    // controller auth admin
    public function login()
    {
        include 'header.php';

        echo '<h1>' . adm_translate('Administration') . '</h1>
        <div id ="adm_men">
            <h2 class="mb-3"><i class="fas fa-sign-in-alt fa-lg align-middle me-2"></i>' . adm_translate('Connexion') . '</h2>
            <form action="admin.php" method="post" id="adminlogin" name="adminlogin">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="mb-3 form-floating">
                            <input id="aid" class="form-control" type="text" name="aid" maxlength="20" placeholder="' . adm_translate('Administrateur ID') . '" required="required" />
                            <label for="aid">' . adm_translate('Administrateur ID') . '</label>
                        </div>
                        <span class="help-block text-end"><span id="countcar_aid"></span></span>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3 form-floating">
                            <input id="pwd" class="form-control" type="password" name="pwd" maxlength="18" placeholder="' . adm_translate('Mot de Passe') . '" required="required" />
                            <label for="pwd">' . adm_translate('Mot de Passe') . '</label>
                        </div>
                        <span class="help-block text-end"><span id="countcar_pwd"></span></span>
                    </div>
                </div>
                <button class="btn btn-primary btn-lg" type="submit">' . adm_translate('Valider') . '</button>
                <input type="hidden" name="op" value="login" />
            </form>
            <script type="text/javascript">
                //<![CDATA[
                    document.adminlogin.aid.focus();
                    $(document).ready(function() {
                        inpandfieldlen("pwd",18);
                        inpandfieldlen("aid",20);
                    });
                //]]>
            </script>';

        $arg1 = 'var formulid =["adminlogin"];';

        Validation::adminFoot('fv', '', $arg1, '');
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
    }
    
}
