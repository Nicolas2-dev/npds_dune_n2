<?php

namespace App\Http\Controllers\Front\User;

use IntlDateFormatter;
use App\Support\Sanitize;
use App\Support\Facades\Auth;
use App\Support\Facades\Date;
use App\Support\Security\Hack;
use App\Support\Facades\Cookie;
use App\Support\Facades\Editeur;
use App\Support\Facades\UserMenu;
use App\Http\Controllers\Core\FrontBaseController;


class UserJournal extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        /*
        switch ($op) {

            case 'editjournal':
                if ($user) {
                    $this->editjournal();
                } else {
                    Header('Location: index.php');
                }
                break;

            case 'savejournal':
                settype($datetime, 'integer');

                $this->savejournal($uid, $journal, $datetime);
                break;
        }
        */

        parent::initialize();
    }

    function editjournal()
    {
        global $user;

        //include 'header.php';

        $userinfo = Auth::getUserInfo($user);

        UserMenu::memberMenu($userinfo['mns'], $userinfo['uname']);

        echo '<h2 class="mb-3">' . translate('Editer votre journal') . '</h2>
        <form action="user.php" method="post" name="adminForm">
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <textarea class="tin form-control" rows="25" name="journal">' . $userinfo['user_journal'] . '</textarea>'
            . Editeur::affEditeur('journal', '') . '
                </div>
            </div>
            <input type="hidden" name="uname" value="' . $userinfo['uname'] . '" />
            <input type="hidden" name="uid" value="' . $userinfo['uid'] . '" />
            <input type="hidden" name="op" value="savejournal" />
            <div class="mb-3 row">
                <div class="col-12">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="datetime" name="datetime" value="1" />
                    <label class="form-check-label" for="datetime">' . translate('Ajouter la date et l\'heure') . '</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-12">
                    <input class="btn btn-primary" type="submit" value="' . translate('Sauvez votre journal') . '" />
                </div>
            </div>
        </form>';

        //include 'footer.php';
    }

    function savejournal($uid, $journal, $datetime)
    {
        global $user;

        $cookie = Cookie::cookieDecode($user);

        $result = sql_query("SELECT uid 
                            FROM " . sql_prefix('users') . " 
                            WHERE uname='$cookie[1]'");

        list($vuid) = sql_fetch_row($result);

        if ($uid == $vuid) {
            include 'modules/upload/config/config.php';

            if ($DOCUMENTROOT == '') {
                global $DOCUMENT_ROOT;
                $DOCUMENTROOT = ($DOCUMENT_ROOT) ? $DOCUMENT_ROOT : $_SERVER['DOCUMENT_ROOT'];
            }

            $user_dir = $DOCUMENTROOT . $racine . '/storage/users_private/' . $cookie[1];

            if (!is_dir($user_dir)) {
                mkdir($user_dir, 0777);
                $fp = fopen($user_dir . '/index.html', 'w');
                fclose($fp);
                chmod($user_dir . '/index.html', 0644);
            }

            $journal = data_image_to_file_url($journal, 'storage/users_private/' . $cookie[1] . '/jou'); //
            $journal = Hack::removeHack(stripslashes(Sanitize::fixQuotes($journal)));

            if ($datetime) {
                $journalentry = $journal;
                $journalentry .= '<br /><br />';
                $journalentry .= Date::formatTimes(time(), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

                sql_query("UPDATE " . sql_prefix('users') . " 
                        SET user_journal='$journalentry' 
                        WHERE uid='$uid'");
            } else {
                sql_query("UPDATE " . sql_prefix('users') . " 
                        SET user_journal='$journal' 
                        WHERE uid='$uid'");
            }

            Header('Location: user.php');
        } else
            Header('Location: index.php');
    }
}
