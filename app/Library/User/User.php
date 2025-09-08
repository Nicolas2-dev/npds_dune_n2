<?php

namespace App\Library\User;

use Npds\Config\Config;
use App\Library\Auth\Auth;
use App\Library\Spam\Spam;
use App\Library\Forum\Forum;
use App\Library\Theme\Theme;


class User
{

    /**
     * Génère un avatar ou un popover utilisateur.
     *
     * Selon la valeur de `$avpop` :
     * - 1 : Affiche l'avatar seul.
     * - 2 : Affiche l'avatar avec un popover contenant les informations et liens de l'utilisateur.
     *
     * @param string $who Nom de l'utilisateur.
     * @param int $dim Taille de l'avatar (détermine la classe CSS `n-ava-$dim`).
     * @param int $avpop Mode d'affichage : 1 pour avatar seul, 2 pour popover.
     * @return string|null HTML de l'avatar ou du popover, ou null si l'utilisateur n'existe pas.
     */
    public static function userPopover(string $who, int $dim, int $avpop): ?string
    {
        global $user; // global a revoir !

        $result = sql_query("SELECT uname 
                            FROM " . sql_prefix('users') . " 
                            WHERE uname ='$who'");

        if (sql_num_rows($result)) {

            $temp_user = Forum::getUserData($who);

            $socialnetworks = array();
            $posterdata_extend = array();
            $res_id = array();

            $my_rs = '';

            if (!Config::get('user.short_user')) {
                if ($temp_user['uid'] != 1) {

                    $posterdata_extend = Forum::getUserDataExtendFromId($temp_user['uid']);

                    include 'modules/reseaux-sociaux/config/config.php';
                    include 'modules/geoloc/config/config.php';

                    if ($user or Auth::autorisation(-127)) {
                        if ($posterdata_extend['M2'] != '') {
                            $socialnetworks = explode(';', $posterdata_extend['M2']);

                            foreach ($socialnetworks as $socialnetwork) {
                                $res_id[] = explode('|', $socialnetwork);
                            }

                            sort($res_id);
                            sort($rs);

                            foreach ($rs as $v1) {
                                foreach ($res_id as $y1) {
                                    $k = array_search($y1[0], $v1);

                                    if (false !== $k) {
                                        $my_rs .= '<a class="me-2 " href="';

                                        if ($v1[2] == 'skype') {
                                            $my_rs .= $v1[1] . $y1[1] . '?chat';
                                        } else {
                                            $my_rs .= $v1[1] . $y1[1];
                                        }

                                        $my_rs .= '" target="_blank"><i class="fab fa-' . $v1[2] . ' fa-lg fa-fw mb-2"></i></a> ';
                                        break;
                                    } else {
                                        $my_rs .= '';
                                    }
                                }
                            }
                        }
                    }
                }
            }

            settype($ch_lat, 'string');

            $useroutils = '';

            if ($user or Auth::autorisation(-127)) {
                if ($temp_user['uid'] != 1 and $temp_user['uid'] != '') {
                    $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="user.php?op=userinfo&amp;uname=' . $temp_user['uname'] . '" target="_blank" title="' . translate("Profil") . '" ><i class="fa fa-lg fa-user align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Profil") . '</span></a></li>';
                }

                if ($temp_user['uid'] != 1 and $temp_user['uid'] != '') {
                    $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="powerpack.php?op=instant_message&amp;to_userid=' . urlencode($temp_user['uname']) . '" title="' . translate("Envoyer un message interne") . '" ><i class="far fa-lg fa-envelope align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Message") . '</span></a></li>';
                }

                if ($temp_user['femail'] != '') {
                    $useroutils .= '<li><a class="dropdown-item  text-center text-md-start" href="mailto:' . Spam::antiSpam($temp_user['femail'], 1) . '" target="_blank" title="' . translate("Email") . '" ><i class="fa fa-at fa-lg align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Email") . '</span></a></li>';
                }

                if ($temp_user['uid'] != 1 and array_key_exists($ch_lat, $posterdata_extend)) {
                    if ($posterdata_extend[$ch_lat] != '') {
                        $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="modules.php?ModPath=geoloc&amp;ModStart=geoloc&op=u' . $temp_user['uid'] . '" title="' . translate("Localisation") . '" ><i class="fas fa-map-marker-alt fa-lg align-middle fa-fw">&nbsp;</i><span class="ms-2 d-none d-md-inline">' . translate("Localisation") . '</span></a></li>';
                    }
                }
            }

            if ($temp_user['url'] != '') {
                $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="' . $temp_user['url'] . '" target="_blank" title="' . translate("Visiter ce site web") . '"><i class="fas fa-external-link-alt fa-lg align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Visiter ce site web") . '</span></a></li>';
            }

            if ($temp_user['mns']) {
                $useroutils .= '<li><a class="dropdown-item text-center text-md-start" href="minisite.php?op=' . $temp_user['uname'] . '" target="_blank" title="' . translate("Visitez le minisite") . '" ><i class="fa fa-lg fa-desktop align-middle fa-fw"></i><span class="ms-2 d-none d-md-inline">' . translate("Visitez le minisite") . '</span></a></li>';
            }

            if (stristr($temp_user['user_avatar'], 'users_private')) {
                $imgtmp = $temp_user['user_avatar'];
            } else {
                $imgtmp = Theme::themeImage('forum/avatar/' . $temp_user['user_avatar']) ?: 'assets/images/forum/avatar/' . $temp_user['user_avatar'];
            }

            $userpop = $avpop == 1
                ? '<img class="btn-outline-primary img-thumbnail img-fluid n-ava-' . $dim . ' me-2" src="' . $imgtmp . '" alt="' . $temp_user['uname'] . '" loading="lazy" />'
                : '<div class="dropdown d-inline-block me-4 dropend">
                    <a class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <img class=" btn-outline-primary img-fluid n-ava-' . $dim . ' me-0" src="' . $imgtmp . '" alt="' . $temp_user['uname'] . '" loading="lazy" />
                    </a>
                    <ul class="dropdown-menu" data-bs-theme="light" >
                        <li><span class="dropdown-item-text text-center py-0 my-0">
                            <img class="btn-outline-primary img-thumbnail img-fluid n-ava-64 me-2" src="' . $imgtmp . '" alt="' . $temp_user['uname'] . '" loading="lazy" />
                        </span></li>
                        <li><h6 class="dropdown-header text-center py-0 my-0">' . $who . '</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        ' . $useroutils . '
                        <li><hr class="dropdown-divider"></li>
                        <li><div class="mx-auto text-center" style="max-width:170px;">' . $my_rs . '</div>
                    </ul>
                    </div>';

            return $userpop;
        }

        return null;
    }

}
