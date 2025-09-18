<?php

namespace App\Http\Controllers\Front\Banners;

use IntlDateFormatter;
use Npds\Config\Config;
use App\Support\Sanitize;
use App\Support\Facades\Url;
use App\Support\Facades\Auth;
use App\Support\Facades\Date;
use App\Support\Facades\Banner;
use App\Support\Facades\Mailer;
use App\Support\Facades\Language;
use Npds\Support\Facades\Request;
use App\Support\Facades\Validation;
use App\Http\Controllers\Core\FrontBaseController;


class Banners extends FrontBaseController
{

    protected int $pdst = 1;

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        /*
        switch ($op) {

            case 'click':
                $this->clickbanner($bid);
                break;

            case 'login':
                $this->clientlogin();
                break;

            case 'Ok':
                $this->bannerstats($login, $pass);
                break;

            case translate('Changer'):
                $this->change_banner_url_by_client($login, $pass, $cid, $bid, $url);
                break;

            case 'EmailStats':
                $this->EmailStats($login, $cid, $bid);
                break;

            default:
                if ($banners) {
                    $this->viewbanner();
                } else {
                    Url::redirectUrl('index.php');
                }
                break;
        }
        */

        parent::initialize();
    }

    public function viewBanner()
    {
        if (Config::get('banner.banners')) {

            $okprint = false;

            $while_limit = 3;
            $while_cpt   = 0;

            $bresult = sql_query("SELECT bid 
                                FROM " . sql_prefix('banner') . " 
                                WHERE userlevel!='9'");

            $numrows = sql_num_rows($bresult);

            while ((!$okprint) and ($while_cpt < $while_limit)) {

                // More efficient random stuff, thanks to Cristian Arroyo from http://www.planetalinux.com.ar
                if ($numrows > 0) {
                    //mt_srand((float)microtime() * 1000000);
                    //$bannum = mt_rand(0, $numrows);

                    $bannum = random_int(0, $numrows);
                } else {
                    break;
                }

                $bresult2 = sql_query("SELECT bid, userlevel 
                                    FROM " . sql_prefix('banner') . " 
                                    WHERE userlevel!='9' 
                                    LIMIT $bannum,1");

                list($bid, $userlevel) = sql_fetch_row($bresult2);

                if ($userlevel == 0) {
                    $okprint = true;
                } else {
                    if ($userlevel == 1) {
                        if (Auth::securStatic('member')) {
                            $okprint = true;
                        }
                    }

                    if ($userlevel == 3) {
                        if (Auth::securStatic('admin')) {
                            $okprint = true;
                        }
                    }
                }

                $while_cpt = $while_cpt + 1;
            }

            // Le risque est de sortir sans un BID valide
            if (!isset($bid)) {
                $rowQ1 = Q_Select("SELECT bid 
                                FROM " . sql_prefix('banner') . " 
                                WHERE userlevel='0' 
                                LIMIT 0,1", 86400);

                if ($rowQ1) {

                    $myrow  = $rowQ1[0]; // erreur à l'install quand on n'a pas de banner dans la base ....
                    $bid    = $myrow['bid'];

                    $okprint = true;
                }
            }

            if ($okprint) {

                $myhost = Request::getip();

                if (Config::get('banner.myIP') != $myhost) {
                    sql_query("UPDATE " . sql_prefix('banner') . " 
                            SET impmade=impmade+1 
                            WHERE bid='$bid'");
                }

                if (($numrows > 0) and ($bid)) {
                    $aborrar = sql_query("SELECT cid, imptotal, impmade, clicks, imageurl, clickurl, date 
                                        FROM " . sql_prefix('banner') . " 
                                        WHERE bid='$bid'");

                    list($cid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date) = sql_fetch_row($aborrar);

                    if ($imptotal == $impmade) {
                        sql_query("INSERT INTO " . sql_prefix('bannerfinish') . " 
                                VALUES (NULL, '$cid', '$impmade', '$clicks', '$date', now())");

                        sql_query("DELETE FROM " . sql_prefix('banner') . " 
                                WHERE bid='$bid'");
                    }

                    if ($imageurl != '') {
                        echo '<a href="banners.php?op=click&amp;bid=' . $bid . '" target="_blank">
                            <img class="img-fluid" src="' . Language::affLangue($imageurl) . '" alt="banner" loading="lazy" />
                        </a>';
                    } else {
                        if (stristr($clickurl, '.txt')) {
                            if (file_exists($clickurl)) {
                                include_once $clickurl;
                            }
                        } else {
                            echo $clickurl;
                        }
                    }
                }
            }
        } else {
            Url::redirectUrl('index.php');
        }
    }

    public function clickBanner(int $bid)
    {
        $bresult = sql_query("SELECT clickurl 
                            FROM " . sql_prefix('banner') . " 
                            WHERE bid='$bid'");

        list($clickurl) = sql_fetch_row($bresult);

        sql_query("UPDATE " . sql_prefix('banner') . " 
                SET clicks=clicks+1 
                WHERE bid='$bid'");

        sql_free_result($bresult);

        if ($clickurl == '') {
            $clickurl = site_url();
        }

        Header('Location: ' . Language::affLangue($clickurl));
    }

    public function clientLogin()
    {
        Banner::headerPage();

        echo '<div class="card card-body mb-3">
                <h3 class="mb-4"><i class="fas fa-sign-in-alt fa-lg me-3 align-middle"></i>' . translate('Connexion') . '</h3>
                <form id="loginbanner" action="banners.php" method="post">
                    <fieldset>
                    <div class="form-floating mb-3">
                        <input class="form-control" type="text" id="login" name="login" maxlength="10" required="required" />
                        <label for="login">' . translate('Identifiant') . '</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" type="password" id="pass" name="pass" maxlength="10" required="required" />
                        <label for="pass">' . translate('Mot de passe') . '</label>
                        <span class="help-block">' . translate('Merci de saisir vos informations') . '</span>
                    </div>
                    <input type="hidden" name="op" value="Ok" />
                    <button class="btn btn-primary my-3" type="submit">' . translate('Valider') . '</button>
                    </div>
                    </fieldset>
                </form>
            </div>';

        $arg1 = 'var formulid=["loginbanner"];';

        Validation::adminFoot('fv', '', $arg1, 'no');

        Banner::footerPage();
    }

    public function incorrectLogin()
    {
        Banner::headerPage();

        echo '<div class="alert alert-danger lead">
            ' . translate('Identifiant incorrect !') . '
            <br />
            <button class="btn btn-secondary mt-2" onclick="javascript:history.go(-1)" >' . translate('Retour en arrière') . '</button>
        </div>';

        Banner::footerPage();
    }

    public function bannerStats(string $login, string $pass)
    {
        $result = sql_query("SELECT cid, name, passwd 
                            FROM " . sql_prefix('bannerclient') . " 
                            WHERE login='$login'");

        list($cid, $name, $passwd) = sql_fetch_row($result);

        if ($login == '' and $pass == '' or $pass == '') {
            $this->incorrectLogin();

        } else {
            if ($pass == $passwd) {
                Banner::headerPage();

                echo '<h3>' . translate('Bannières actives pour') . ' ' . $name . '</h3>
                <table data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-show-columns="true" data-icons="icons" data-icons-prefix="fa">
                    <thead>
                    <tr>
                        <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">ID</th>
                        <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">' . translate('Réalisé') . '</th>
                        <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">' . translate('Impressions') . '</th>
                        <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">' . translate('Imp. restantes') . '</th>
                        <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">' . translate('Clics') . '</th>
                        <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">% ' . translate('Clics') . '</th>
                        <th class="n-t-col-xs-1" data-halign="center" data-align="right">' . translate('Fonctions') . '</th>
                    </tr>
                    </thead>
                    <tbody>';

                $result = sql_query("SELECT bid, imptotal, impmade, clicks, date 
                                    FROM " . sql_prefix('banner') . " 
                                    WHERE cid='$cid'");

                while (list($bid, $imptotal, $impmade, $clicks, $date) = sql_fetch_row($result)) {

                    $percent = $impmade == 0 ? '0' : substr(100 * $clicks / $impmade, 0, 5);
                    $left = $imptotal == 0 ? translate('Illimité') : $imptotal - $impmade;

                    echo '<tr>
                        <td>' . $bid . '</td>
                        <td>' . $impmade . '</td>
                        <td>' . $imptotal . '</td>
                        <td>' . $left . '</td>
                        <td>' . $clicks . '</td>
                        <td>' . $percent . '%</td>
                        <td>
                            <a href="banners.php?op=EmailStats&amp;login=' . $login . '&amp;cid=' . $cid . '&amp;bid=' . $bid . '" >
                                <i class="far fa-envelope fa-lg me-2 tooltipbyclass" data-bs-placement="top" title="E-mail Stats"></i>
                            </a>
                        </td>
                    </tr>';
                }

                echo '</tbody>
                </table>
                <div class="lead my-3">
                    <a href="' . site_url() . '" target="_blank">' . Config::get('app.sitename') . '</a>
                </div>';

                $result = sql_query("SELECT bid, imageurl, clickurl 
                                    FROM " . sql_prefix('banner') . " 
                                    WHERE cid='$cid'");

                while (list($bid, $imageurl, $clickurl) = sql_fetch_row($result)) {

                    //$numrows = sql_num_rows($result); ??

                    echo '<div class="card card-body mb-3">';

                    if ($imageurl != '') {
                        echo '<p><img src="' . Language::affLangue($imageurl) . '" class="img-fluid" />'; // pourquoi affLangue ??
                    } else {
                        echo '<p>';
                        echo $clickurl;
                    }

                    echo '<h4 class="mb-2">Banner ID : ' . $bid . '</h4>';

                    if ($imageurl != '') {
                        echo '<p>' . translate('Cette bannière est affichée sur l\'url') . ' : <a href="' . Language::affLangue($clickurl) . '" target="_Blank" >[ URL ]</a></p>';
                    }

                    echo '<form action="banners.php" method="get">';

                    if ($imageurl != '') {
                        echo '<div class="mb-3 row">
                            <label class="control-label col-sm-12" for="url">' . translate('Changer') . ' URL</label>
                            <div class="col-sm-12">
                                <input class="form-control" type="text" name="url" maxlength="200" value="' . $clickurl . '" />
                            </div>
                        </div>';
                    } else {
                        echo '<div class="mb-3 row">
                            <label class="control-label col-sm-12" for="url">' . translate('Changer') . ' URL</label>
                            <div class="col-sm-12">
                                <input class="form-control" type="text" name="url" maxlength="200" value="' . htmlentities($clickurl, ENT_QUOTES, 'UTF-8') . '" />
                            </div>
                        </div>';
                    }

                    echo '<input type="hidden" name="login" value="' . $login . '" />
                            <input type="hidden" name="bid" value="' . $bid . '" />
                            <input type="hidden" name="pass" value="' . $pass . '" />
                            <input type="hidden" name="cid" value="' . $cid . '" />
                            <input class="btn btn-primary" type="submit" name="op" value="' . translate('Changer') . '" />
                            </form>
                        </p>
                    </div>';
                }

                // Finnished Banners
                echo "<br />";

                echo '<h3>' . translate('Bannières terminées pour') . ' ' . $name . '</h3>
                <table data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-show-columns="true" data-icons="icons" data-icons-prefix="fa">
                    <thead>
                    <tr>
                        <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">ID</td>
                        <th data-halign="center" data-align="right" data-sortable="true">' . translate('Impressions') . '</th>
                        <th data-halign="center" data-align="right" data-sortable="true">' . translate('Clics') . '</th>
                        <th class="n-t-col-xs-1" data-halign="center" data-align="right" data-sortable="true">% ' . translate('Clics') . '</th>
                        <th data-halign="center" data-align="right" data-sortable="true">' . translate('Date de début') . '</th>
                        <th data-halign="center" data-align="right" data-sortable="true">' . translate('Date de fin') . '</th>
                    </tr>
                    </thead>
                    <tbody>';

                $result = sql_query("SELECT bid, impressions, clicks, datestart, dateend 
                                    FROM " . sql_prefix('bannerfinish') . " 
                                    WHERE cid='$cid'");

                while (list($bid, $impressions, $clicks, $datestart, $dateend) = sql_fetch_row($result)) {

                    $percent = substr(100 * $clicks / $impressions, 0, 5);

                    echo '<tr>
                        <td>' . $bid . '</td>
                        <td>' . Sanitize::wrh($impressions) . '</td>
                        <td>' . $clicks . '</td>
                        <td>' . $percent . ' %</td>
                        <td><small>' . Date::formatTimes($datestart, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT) . '</small></td>
                        <td><small>' . Date::formatTimes($dateend, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT) . '</small></td>
                    </tr>';
                }

                echo '</tbody>
                </table>';

                Validation::adminFoot('fv', '', '', 'no');

                Banner::footerPage();
            } else {
                $this->incorrectLogin();
            }
        }
    }

    public function emailStats(string $login, int $cid, int $bid)
    {
        $result = sql_query("SELECT login 
                            FROM " . sql_prefix('bannerclient') . " 
                            WHERE cid='$cid'");

        list($loginBD) = sql_fetch_row($result);

        if ($login == $loginBD) {

            $result2 = sql_query("SELECT name, email 
                                FROM " . sql_prefix('bannerclient') . " 
                                WHERE cid='$cid'");

            list($name, $email) = sql_fetch_row($result2);

            if ($email == '') {
                Banner::headerPage();

                echo '<p align="center">
                    <br />
                    ' . translate('Les statistiques pour la bannières ID') . ' : ' . $bid . ' ' . translate('ne peuvent pas être envoyées.') . '
                    <br />
                    <br />
                    ' . translate('Email non rempli pour : ') . ' $name
                    <br />
                    <br />
                    <a href="javascript:history.go(-1)" >' . translate('Retour en arrière') . '</a>
                </p>';

                Banner::footerPage();
            } else {
                $result = sql_query("SELECT bid, imptotal, impmade, clicks, imageurl, clickurl, date 
                                    FROM " . sql_prefix('banner') . " 
                                    WHERE bid='$bid' 
                                    AND cid='$cid'");

                list($bid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date) = sql_fetch_row($result);

                $percent = $impmade == 0 ? '0' : substr(100 * $clicks / $impmade, 0, 5);

                if ($imptotal == 0) {
                    $left = translate('Illimité');
                    $imptotal = translate('Illimité');
                } else {
                    $left = $imptotal - $impmade;
                }

                $fecha = Date::formatTimes(time(), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

                $subject = html_entity_decode(translate('Bannières - Publicité'), ENT_COMPAT | ENT_HTML401, 'UTF-8') . ' : ' . Config::get('app.sitename');

                $message = nl2br(
                    "Client : $name\n"
                        . translate('Bannière') . " ID : $bid\n"
                        . translate('Bannière') . " Image : $imageurl\n"
                        . translate('Bannière') . " URL : $clickurl\n\n"
                        . "Impressions " . translate('Réservées') . " : $imptotal\n"
                        . "Impressions " . translate('Réalisées') . " : $impmade\n"
                        . "Impressions " . translate('Restantes') . " : $left\n"
                        . "Clicks " . translate('Reçus') . " : $clicks\n"
                        . "Clicks " . translate('Pourcentage') . " : $percent%\n\n"
                        . translate('Rapport généré le') . ' : ' . $fecha . "\n\n"
                );

                if (Config::has('signature.signature')) {

                    $signature = Config::get('signature.signature');

                    if (!empty($signature)) {
                        $message .= $signature;
                    }
                }

                Mailer::sendEmail($email, $subject, $message, '', true, 'html', '');

                Banner::headerPage();

                echo '<div class="card bg-light">
                    <div class="card-body"
                    <p>' . $fecha . '</p>
                    <p>' . translate('Les statistiques pour la bannières ID') . ' : ' . $bid . ' ' . translate('ont été envoyées.') . '</p>
                    <p>' . $email . ' : Client : ' . $name . '</p>
                    <p><a href="javascript:history.go(-1)" class="btn btn-primary">' . translate('Retour en arrière') . '</a></p>
                    </div>
                </div>';
            }
        } else {
            Banner::headerPage();

            echo '<div class="alert alert-danger">
                ' . translate('Identifiant incorrect !') . '
                <br />
                ' . translate('Merci de') . ' 
                <a href="banners.php?op=login" class="alert-link">
                    ' . translate('vous reconnecter.') . '
                </a>
            </div>';
        }

        Banner::footerPage();
    }

    public function changeBannerUrlByClient(string $login, string $pass, int $cid, int $bid, string $url)
    {
        Banner::headerPage();

        $result = sql_query("SELECT passwd 
                            FROM " . sql_prefix('bannerclient') . " 
                            WHERE cid='$cid'");

        list($passwd) = sql_fetch_row($result);

        if (!empty($pass) and $pass == $passwd) {
            sql_query("UPDATE " . sql_prefix('banner') . " 
                    SET clickurl='$url' 
                    WHERE bid='$bid'");

            echo '<div class="alert alert-success">
                ' . translate('Vous avez changé l\'url de la bannière') . '
                <br />
                <a href="javascript:history.go(-1)" class="alert-link">
                    ' . translate('Retour en arrière') . '
                </a>
            </div>';
        } else {
            echo '<div class="alert alert-danger">
                ' . translate('Identifiant incorrect !') . '
                <br />
                ' . translate('Merci de') . ' 
                <a href="banners.php?op=login" class="alert-link">
                    ' . translate('vous reconnecter.') . '
                </a>
            </div>';
        }

        Banner::footerPage();
    }

}
