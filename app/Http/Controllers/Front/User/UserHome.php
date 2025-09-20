<?php

namespace App\Http\Controllers\Front\User;

use App\Support\Sanitize;
use App\Support\Facades\Auth;
use App\Support\Security\Hack;
use App\Support\Facades\Cookie;
use App\Support\Facades\Password;
use App\Support\Facades\UserMenu;
use App\Support\Facades\Validation;
use App\Library\Cache\SuperCacheManager;
use App\Http\Controllers\Core\FrontBaseController;


class UserHomme extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        /*
        switch ($op) {

            case 'edithome':
                if ($user) {
                    $this->edithome();
                } else {
                    Header('Location: index.php');
                }
                break;

            case 'savehome':
                settype($ublockon, 'integer');

                $this->savehome($uid, $uname, $theme, $storynum, $ublockon, $ublock);
                break;
        }
        */

        parent::initialize();
    }

    function edithome()
    {
        global $user, $Default_Theme, $Default_Skin;

        //include 'header.php';

        $userinfo = Auth::getUserInfo($user);

        UserMenu::memberMenu($userinfo['mns'], $userinfo['uname']);

        if ($userinfo['theme'] == '') {
            $userinfo['theme'] = "$Default_Theme+$Default_Skin";
        }

        echo '<h2 class="mb-3">' . translate('Editer votre page principale') . '</h2>
        <form id="changehome" action="user.php" method="post">
        <div class="mb-3 row">
            <label class="col-form-label col-sm-7" for="storynum">' . translate('Nombre d\'articles sur la page principale') . ' (max. 127) :</label>
            <div class="col-sm-5">
                <input class="form-control" type="text" min="0" max="127" id="storynum" name="storynum" maxlength="3" value="' . $userinfo['storynum'] . '" />
            </div>
        </div>';

        $sel = $userinfo['ublockon'] == 1 ? 'checked="checked"' : '';

        echo '<div class="mb-3 row">
            <div class="col-sm-10">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="ublockon" name="ublockon" value="1" ' . $sel . ' />
                    <label class="form-check-label" for="ublockon">' . translate('Activer votre menu personnel') . '</label>
                </div>
            </div>
        </div>
        <ul>
            <li>' . translate('Validez cette option et le texte suivant apparaîtra sur votre page d\'accueil') . '</li>
            <li>' . translate('Vous pouvez utiliser du code html, pour créer un lien par exemple') . '</li>
        </ul>
        <div class="mb-3 row">
            <div class="col-sm-12">
                <textarea class="form-control" rows="20" name="ublock">' . $userinfo['ublock'] . '</textarea>
            </div>
        </div>
            <div class="mb-3 row">
                <input type="hidden" name="theme" value="' . $userinfo['theme'] . '" />
                <input type="hidden" name="uname" value="' . $userinfo['uname'] . '" />
                <input type="hidden" name="uid" value="' . $userinfo['uid'] . '" />
                <input type="hidden" name="op" value="savehome" />
                <div class="col-sm-12">
                    <input class="btn btn-primary" type="submit" value="' . translate('Sauver les modifications') . '" />
                </div>
            </div>
        </form>';

        $fv_parametres = '
            storynum: {
                validators: {
                    regexp: {
                        regexp:/^[1-9](\d{0,2})$/,
                        message: "0-9"
                    },
                    between: {
                        min: 1,
                        max: 127,
                        message: "1 ... 127"
                    }
                }
            },';

        $arg1 = 'var formulid=["changehome"];';

        Validation::adminFoot('fv', $fv_parametres, $arg1, 'foo');
    }

    function savehome($uid, $uname, $theme, $storynum, $ublockon, $ublock)
    {
        global $user;

        $cookie = Cookie::cookieDecode($user);
        $check = $cookie[1];

        $result = sql_query("SELECT uid 
                            FROM " . sql_prefix('users') . " 
                            WHERE uname='$check'");

        list($vuid) = sql_fetch_row($result);

        if (($check == $uname) and ($uid == $vuid)) {
            $ublockon = $ublockon ? 1 : 0;

            $ublock = Hack::removeHack(Sanitize::fixQuotes($ublock));

            sql_query("UPDATE " . sql_prefix('users') . " 
                    SET storynum='$storynum', ublockon='$ublockon', ublock='$ublock' 
                    WHERE uid='$uid'");

            $userinfo = Auth::getUserInfo($user);

            Password::docookie($userinfo['uid'], $userinfo['uname'], $userinfo['pass'], $userinfo['storynum'], $userinfo['umode'], $userinfo['uorder'], $userinfo['thold'], $userinfo['noscore'], $userinfo['ublockon'], $userinfo['theme'], $userinfo['commentmax'], '');

            // Include cache manager for purge cache Page
            $cache_obj = new SuperCacheManager();
            $cache_obj->usercacheCleanup();

            Header('Location: user.php?op=edithome');
        } else {
            Header('Location: index.php');
        }
    }

}
