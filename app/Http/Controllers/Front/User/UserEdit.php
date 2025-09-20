<?php

namespace App\Http\Controllers\Front\User;

use App\Support\Sanitize;
use App\Support\Facades\Auth;
use App\Support\Security\Hack;
use App\Support\Facades\Cookie;
use App\Library\User\UserMessage;
use App\Support\Facades\Password;
use App\Support\Facades\UserMenu;
use App\Library\User\UserValidator;
use App\Library\User\Traits\UserLogout;
use App\Http\Controllers\Core\FrontBaseController;


class UserEdit extends FrontBaseController
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

            case 'edituser':
                if ($user) {
                    $this->edituser();
                } else {
                    $this->Header('Location: index.php');
                }
                break;

            case 'saveuser':
                $past = time() - 300;

                sql_query("DELETE FROM " . sql_prefix('session') . " 
                        WHERE time < $past");

                $result = sql_query("SELECT time 
                                    FROM " . sql_prefix('session') . " 
                                    WHERE username='$cookie[1]'");

                if (sql_num_rows($result) == 1) {

                    // CheckBox
                    settype($attach, 'integer');
                    settype($user_viewemail, 'integer');
                    settype($usend_email, 'integer');
                    settype($uis_visible, 'integer');
                    settype($user_lnl, 'integer');
                    settype($raz_avatar, 'integer');

                    $this->saveuser($uid, $name, $uname, $email, $femail, $url, $pass, $vpass, $bio, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $attach, $usend_email, $uis_visible, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1, $MAX_FILE_SIZE, $raz_avatar);
                } else {
                    Header('Location: user.php');
                }
                break;
        }
        */

        parent::initialize();
    }

    function edituser()
    {
        global $user, $smilies, $short_user, $subscribe, $member_invisible, $avatar_size;

        //include 'header.php';

        $userinfo = Auth::getUserInfo($user);

        UserMenu::memberMenu($userinfo['mns'], $userinfo['uname']);

        global $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1;

        $result = sql_query("SELECT C1, C2, C3, C4, C5, C6, C7, C8, M1, M2, T1, T2, B1 
                            FROM " . sql_prefix('users_extend') . " 
                            WHERE uid='" . $userinfo['uid'] . "'");

        list($C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1) = sql_fetch_row($result);

        showimage();

        include 'library/sform/extend-user/mod_extend-user.php';
        //include 'footer.php';
    }

    function saveuser($uid, $name, $uname, $email, $femail, $url, $pass, $vpass, $bio, $user_avatar, $user_occ, $user_from, $user_intrest, $user_sig, $user_viewemail, $attach, $usend_email, $uis_visible, $user_lnl, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1, $MAX_FILE_SIZE, $raz_avatar)
    {
        global $user, $userinfo, $minpass;

        $cookie = Cookie::cookieDecode($user);
        $check = $cookie[1];

        $result = sql_query("SELECT uid, email 
                            FROM " . sql_prefix('users') . " 
                            WHERE uname='$check'");

        list($vuid, $vemail) = sql_fetch_row($result);

        if (($check == $uname) and ($uid == $vuid)) {
            if ((isset($pass)) && ("$pass" != "$vpass")) {
                //message_error('<i class="fa fa-exclamation me-2"></i>' . translate('Les mots de passe sont différents. Ils doivent être identiques.') . '<br />', '');
                UserMessage::error('<i class="fa fa-exclamation me-2"></i>' . translate('Les mots de passe sont différents. Ils doivent être identiques.') . '<br />', '');
            
            } elseif (($pass != '') && (strlen($pass) < $minpass)) {
                //message_error('<i class="fa fa-exclamation me-2"></i>' . translate('Désolé, votre mot de passe doit faire au moins') . ' <strong>' . $minpass . '</strong> ' . translate('caractères') . '<br />', '');
                UserMessage::error('<i class="fa fa-exclamation me-2"></i>' . translate('Désolé, votre mot de passe doit faire au moins') . ' <strong>' . $minpass . '</strong> ' . translate('caractères') . '<br />', '');

            } else {

                //$stop = userCheck('edituser', $email);
                $stop = UserValidator::validateUser('edituser', $email);

                if (!$stop) {
                    $contents = '';
                    $filename = 'storage/banned/usersbadmail.txt';
                    $handle = fopen($filename, 'r');

                    if (filesize($filename) > 0) {
                        $contents = fread($handle, filesize($filename));
                    }

                    fclose($handle);

                    $re = '/#' . $uid . '\|(\d+)/m';
                    $maj = preg_replace($re, '', $contents);

                    $file = fopen('storage/banned/usersbadmail.txt', 'w');
                    fwrite($file, $maj);
                    fclose($file);

                    if ($bio) {
                        $bio = Sanitize::fixQuotes(strip_tags($bio));
                    }

                    $t = $attach ? 1 : 0;
                    $a = $user_viewemail ? 1 : 0;
                    $u = $usend_email ? 1 : 0;
                    $v = $uis_visible ? 0 : 1;
                    $w = $user_lnl ? 1 : 0;

                    include_once 'modules/upload/config/config.php';

                    global $avatar_size;
                    if (!$avatar_size) {
                        $avatar_size = '80*100';
                    }

                    $avatar_limit = explode('*', $avatar_size);
                    $rep = $DOCUMENTROOT != '' ? $DOCUMENTROOT : $_SERVER['DOCUMENT_ROOT'];

                    if ($B1 != 'none') {
                        global $language;

                        include_once 'modules/upload/language/' . $language . 'upload.lang-' . $language . '.php';
                        //include_once 'modules/upload/library/clsUpload.php';

                        $upload = new Upload();

                        $upload->maxupload_size = $MAX_FILE_SIZE;

                        $field1_filename = trim($upload->getFileName("B1"));
                        $suffix = strtoLower(substr(strrchr($field1_filename, '.'), 1));

                        if (($suffix == 'gif') or ($suffix == 'jpg') or ($suffix == 'png') or ($suffix == 'jpeg')) {

                            $field1_filename = Hack::removeHack(preg_replace('#[/\\\:\*\?"<>|]#i', '', rawurldecode($field1_filename)));
                            $field1_filename = preg_replace('#\.{2}|config.php|/etc#i', '', $field1_filename);

                            if ($field1_filename) {
                                if ($autorise_upload_p) {
                                    $user_dir = $racine . '/storage/users_private/' . $uname . '/';

                                    if (!is_dir($rep . $user_dir)) {
                                        @umask(0000);

                                        if (@mkdir($rep . $user_dir, 0777)) {
                                            $fp = fopen($rep . $user_dir . 'index.html', 'w');
                                            fclose($fp);
                                        } else {
                                            $user_dir = $racine . '/storage/users_private/';
                                        }
                                    }
                                } else {
                                    $user_dir = $racine . '/storage/users_private/';
                                }

                                if ($upload->saveAs($uname . '.' . $suffix, $rep . $user_dir, 'B1', true)) {
                                    $old_user_avatar = $user_avatar;
                                    $user_avatar = $user_dir . $uname . '.' . $suffix;
                                    $img_size = @getimagesize($rep . $user_avatar);

                                    if (($img_size[0] > $avatar_limit[0]) or ($img_size[1] > $avatar_limit[1])) {
                                        $raz_avatar = true;
                                    }

                                    if ($racine == '') {
                                        $user_avatar = substr($user_avatar, 1);
                                    }
                                }
                            }
                        }
                    }

                    if ($raz_avatar) {
                        if (strstr($user_avatar, '/users_private')) {
                            @unlink($rep . $user_avatar);
                            @unlink($rep . $old_user_avatar);
                        }

                        $user_avatar = 'blank.gif';
                    }

                    if ($pass != '') {
                        Cookie::cookieDecode($user);

                        $AlgoCrypt  = PASSWORD_BCRYPT;
                        $min_ms     = 100;
                        $options    = ['cost' => Password::getOptimalBcryptCostParameter($pass, $AlgoCrypt, $min_ms),];
                        $hashpass   = password_hash($pass, PASSWORD_BCRYPT, $options);
                        $pass       = crypt($pass, $hashpass);

                        sql_query("UPDATE " . sql_prefix('users') . " 
                                SET name='$name', email='$email', femail='" . Hack::removeHack($femail) . "', url='" . Hack::removeHack($url) . "', pass='$pass', hashkey='1', bio='" . Hack::removeHack($bio) . "', user_avatar='$user_avatar', user_occ='" . Hack::removeHack($user_occ) . "', user_from='" . Hack::removeHack($user_from) . "', user_intrest='" . Hack::removeHack($user_intrest) . "', user_sig='" . Hack::removeHack($user_sig) . "', user_viewemail='$a', send_email='$u', is_visible='$v', user_lnl='$w' 
                                WHERE uid='$uid'");

                        $result = sql_query("SELECT uid, uname, pass, storynum, umode, uorder, thold, noscore, ublockon, theme 
                                            FROM " . sql_prefix('users') . " 
                                            WHERE uname='$uname' 
                                            AND pass='$pass'");

                        if (sql_num_rows($result) == 1) {
                            $userinfo = sql_fetch_assoc($result);

                            Cookie::docookie(
                                $userinfo['uid'],
                                $userinfo['uname'],
                                $userinfo['pass'],
                                $userinfo['storynum'],
                                $userinfo['umode'],
                                $userinfo['uorder'],
                                $userinfo['thold'],
                                $userinfo['noscore'],
                                $userinfo['ublockon'],
                                $userinfo['theme'],
                                $userinfo['commentmax'],
                                '',
                                $skin
                            );
                        }
                    } else {
                        sql_query("UPDATE " . sql_prefix('users') . " 
                                SET name='$name', email='$email', femail='" . Hack::removeHack($femail) . "', url='" . Hack::removeHack($url) . "', bio='" . Hack::removeHack($bio) . "', user_avatar='$user_avatar', user_occ='" . Hack::removeHack($user_occ) . "', user_from='" . Hack::removeHack($user_from) . "', user_intrest='" . Hack::removeHack($user_intrest) . "', user_sig='" . Hack::removeHack($user_sig) . "', user_viewemail='$a', send_email='$u', is_visible='$v', user_lnl='$w' 
                                WHERE uid='$uid'");
                    }
                    sql_query("UPDATE " . sql_prefix('users_status') . " 
                            SET attachsig='$t' 
                            WHERE uid='$uid'");

                    $result = sql_query("SELECT uid 
                                        FROM " . sql_prefix('users_extend') . " 
                                        WHERE uid='$uid'");

                    if (sql_num_rows($result) == 1) {
                        sql_query("UPDATE " . sql_prefix('users_extend') . " 
                                SET C1='" . Hack::removeHack($C1) . "', C2='" . Hack::removeHack($C2) . "', C3='" . Hack::removeHack($C3) . "', C4='" . Hack::removeHack($C4) . "', C5='" . Hack::removeHack($C5) . "', C6='" . Hack::removeHack($C6) . "', C7='" . Hack::removeHack($C7) . "', C8='" . Hack::removeHack($C8) . "', M1='" . Hack::removeHack($M1) . "', M2='" . Hack::removeHack($M2) . "', T1='" . Hack::removeHack($T1) . "', T2='" . Hack::removeHack($T2) . "', B1='$B1'
                                WHERE uid='$uid'");
                    } else {
                        $result = sql_query("INSERT INTO " . sql_prefix('users_extend') . " 
                                            VALUES ('$uid','" . Hack::removeHack($C1) . "', '" . Hack::removeHack($C2) . "', '" . Hack::removeHack($C3) . "', '" . Hack::removeHack($C4) . "', '" . Hack::removeHack($C5) . "', '" . Hack::removeHack($C6) . "', '" . Hack::removeHack($C7) . "', '" . Hack::removeHack($C8) . "', '" . Hack::removeHack($M1) . "', '" . Hack::removeHack($M2) . "', '" . Hack::removeHack($T1) . "', '" . Hack::removeHack($T2) . "', '$B1')");
                    }
                    if ($pass != '') {
                        $this->logout();
                    } else {
                        header('location: user.php?op=edituser');
                    }
                } else {
                    //message_error($stop, '');
                    UserMessage::error($stop, '');
                }
            }
        } else {
            Header('Location: index.php');
        }
    }
}
