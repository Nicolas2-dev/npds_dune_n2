<?php

namespace App\Library\Subscribe;

use Npds\Config\Config;
use App\Library\Mailer\Mailer;


class Subscribe
{

    /**
     * Assure l'envoi d'un mail pour un abonnement à un topic ou forum.
     *
     * @param string $Xtype  Type d'abonnement : "topic" ou "forum"
     * @param int|string $Xtopic ID du topic concerné
     * @param int|string $Xforum ID du forum concerné
     * @param string $Xresume Texte du dernier message à inclure
     * @param int|string $Xsauf ID de l'utilisateur à exclure de l'envoi
     * @return void
     */
    public static function subscribeMail(
        string      $Xtype,
        int|string  $Xtopic,
        int|string  $Xforum,
        string      $Xresume,
        int|string  $Xsauf
    ): void {

        if ($Xtype == 'topic') {
            $result = sql_query("SELECT topictext 
                                FROM " . sql_prefix('topics') . " 
                                WHERE topicid='$Xtopic'");

            list($abo) = sql_fetch_row($result);

            $result = sql_query("SELECT uid 
                                FROM " . sql_prefix('subscribe') . " 
                                WHERE topicid='$Xtopic'");
        }

        if ($Xtype == 'forum') {
            $result = sql_query("SELECT forum_name, arbre 
                                FROM " . sql_prefix('forums') . " 
                                WHERE forum_id='$Xforum'");

            list($abo, $arbre) = sql_fetch_row($result);

            if ($arbre) {
                $hrefX = 'viewtopicH.php';
            } else {
                $hrefX = 'viewtopic.php';
            }

            $resultZ = sql_query("SELECT topic_title 
                                FROM " . sql_prefix('forumtopics') . " 
                                WHERE topic_id='$Xtopic'");

            list($title_topic) = sql_fetch_row($resultZ);

            $result = sql_query("SELECT uid 
                                FROM " . sql_prefix('subscribe') . " 
                                WHERE forumid='$Xforum'");
        }

        include_once 'language/lang-multi.php';

        $nuke_url = Config::get('app.url');

        while (list($uid) = sql_fetch_row($result)) {
            if ($uid != $Xsauf) {

                $resultX = sql_query("SELECT email, user_langue 
                                    FROM " . sql_prefix('users') . " 
                                    WHERE uid='$uid'");

                list($email, $user_langue) = sql_fetch_row($resultX);

                if ($Xtype == 'topic') {
                    $entete = translate_ml($user_langue, "Vous recevez ce Mail car vous vous êtes abonné à : ") . translate_ml($user_langue, "Sujet") . " => " . strip_tags($abo) . "\n\n";
                    $resume = translate_ml($user_langue, "Le titre de la dernière publication est") . " => $Xresume\n\n";

                    $url = translate_ml($user_langue, "L'URL pour cet article est : ") . "<a href=\"$nuke_url/search.php?query=&topic=$Xtopic\">$nuke_url/search.php?query=&topic=$Xtopic</a>\n\n";
                }

                if ($Xtype == 'forum') {
                    $entete = translate_ml($user_langue, "Vous recevez ce Mail car vous vous êtes abonné à : ") . translate_ml($user_langue, "Forum") . " => " . strip_tags($abo) . "\n\n";
                    $resume = translate_ml($user_langue, "Le titre de la dernière publication est") . " => ";

                    $url = translate_ml($user_langue, "L'URL pour cet article est : ") . "<a href=\"$nuke_url/$hrefX?topic=$Xtopic&forum=$Xforum&start=9999#lastpost\">$nuke_url/$hrefX?topic=$Xtopic&forum=$Xforum&start=9999</a>\n\n";

                    if ($Xresume != '') {
                        $resume .= $Xresume . "\n\n";
                    } else {
                        $resume .= $title_topic . "\n\n";
                    }
                }

                $subject = html_entity_decode(translate_ml($user_langue, "Abonnement"), ENT_COMPAT | ENT_HTML401, 'UTF-8') . " / ". Config::get('app.sitename');
                $message = $entete;
                $message .= $resume;
                $message .= $url;

                $message .= Config::get('signature.signature');

                Mailer::sendEmail($email, $subject, $message, '', true, 'html');
            }
        }
    }

    /**
     * Vérifie si un membre est abonné à un topic ou un forum.
     *
     * @param int|string $Xuser ID de l'utilisateur
     * @param string $Xtype Type d'abonnement : "topic" ou "forum"
     * @param int|string $Xclef ID du topic ou du forum
     * @return bool True si l'utilisateur est abonné, false sinon
     */
    public static function subscribeQuery(
        int|string  $Xuser,
        string      $Xtype,
        int|string  $Xclef
    ): bool {
        if ($Xtype == 'topic') {
            $result = sql_query("SELECT topicid 
                                FROM " . sql_prefix('subscribe') . " 
                                WHERE uid='$Xuser' 
                                AND topicid='$Xclef'");
        }

        if ($Xtype == 'forum') {
            $result = sql_query("SELECT forumid 
                                FROM " . sql_prefix('subscribe') . " 
                                WHERE uid='$Xuser' 
                                AND forumid='$Xclef'");
        }

        list($Xtemp) = sql_fetch_row($result);

        if ($Xtemp != '') {
            return true;
        } else {
            return false;
        }
    }
}
