<?php

namespace App\Http\Controllers\Front\Banners;

use Npds\Config\Config;
use App\Support\Facades\Auth;
use App\Support\Facades\Language;
use Npds\Support\Facades\Request;

/**
 * Service de gestion et de rendu des bannières publicitaires.
 *
 * Cette classe permet de récupérer une bannière à afficher sur le site,
 * en respectant les niveaux d'accès des utilisateurs et en comptabilisant
 * les impressions. Elle gère également la fin de vie des bannières
 * (lorsque le nombre d'impressions est atteint) et peut afficher soit
 * une image cliquable, soit un contenu HTML/textuel fourni par la bannière.
 */
class BannerService
{

    /**
     * Récupère et génère le code HTML d'une bannière à afficher.
     *
     * La méthode sélectionne une bannière aléatoire disponible selon le niveau
     * d'accès de l'utilisateur, incrémente le compteur d'impressions, gère
     * la fin de vie de la bannière et retourne le HTML correspondant.
     *
     * @return string Code HTML de la bannière à insérer dans la page.
     */
    public function renderBanner(): string
    {
        $html = '';

        if (!Config::get('banner.banners')) {
            return $html;
        }

        $okprint = false;
        
        $while_limit = 3;
        $while_cpt = 0;

        $bresult = sql_query("SELECT bid FROM " . sql_prefix('banner') . " WHERE userlevel!='9'");
        $numrows = sql_num_rows($bresult);

        while ((!$okprint) && ($while_cpt < $while_limit)) {
            if ($numrows > 0) {
                $bannum = random_int(0, $numrows-1);
            } else {
                break;
            }

            $bresult2 = sql_query("SELECT bid, userlevel FROM " . sql_prefix('banner') . " WHERE userlevel!='9' LIMIT $bannum,1");
            list($bid, $userlevel) = sql_fetch_row($bresult2);

            if ($userlevel == 0 || ($userlevel == 1 && Auth::securStatic('member')) || ($userlevel == 3 && Auth::securStatic('admin'))) {
                $okprint = true;
            }

            $while_cpt++;
        }

        if (!isset($bid)) {
            $rowQ1 = Q_Select("SELECT bid FROM " . sql_prefix('banner') . " WHERE userlevel='0' LIMIT 0,1", 86400);

            if ($rowQ1) {
                $bid = $rowQ1[0]['bid'];
                $okprint = true;
            }
        }

        if ($okprint && $bid) {
            $myhost = Request::getip();

            if (Config::get('banner.myIP') != $myhost) {
                sql_query("UPDATE " . sql_prefix('banner') . " SET impmade=impmade+1 WHERE bid='$bid'");
            }

            $aborrar = sql_query("SELECT cid, imptotal, impmade, clicks, imageurl, clickurl, date FROM " . sql_prefix('banner') . " WHERE bid='$bid'");
            list($cid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date) = sql_fetch_row($aborrar);

            if ($imptotal == $impmade) {
                sql_query("INSERT INTO " . sql_prefix('bannerfinish') . " VALUES (NULL, '$cid', '$impmade', '$clicks', '$date', now())");
                sql_query("DELETE FROM " . sql_prefix('banner') . " WHERE bid='$bid'");
            }

            if ($imageurl != '') {
                $html .= '<a href="banners.php?op=click&amp;bid=' . $bid . '" target="_blank">
                            <img class="img-fluid" src="' . Language::affLangue($imageurl) . '" alt="banner" loading="lazy" />
                        </a>';
            } else {
                if (stristr($clickurl, '.txt') && file_exists($clickurl)) {
                    $html .= file_get_contents($clickurl);
                } else {
                    $html .= $clickurl;
                }
            }
        }

        return $html;
    }

}
