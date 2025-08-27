<?php

namespace App\Library\Subscribe;


class Subscribe
{

    #autodoc subscribe_mail($Xtype, $Xtopic,$Xforum, $Xresume, $Xsauf) : Assure l'envoi d'un mail pour un abonnement
    function subscribe_mail($Xtype, $Xtopic, $Xforum, $Xresume, $Xsauf)
    {
        // $Xtype : topic, forum ... / $Xtopic clause WHERE / $Xforum id of forum / $Xresume Text passed / $Xsauf not this userid
        global $sitename, $nuke_url;

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

                $subject = html_entity_decode(translate_ml($user_langue, "Abonnement"), ENT_COMPAT | ENT_HTML401, 'UTF-8') . " / $sitename";
                $message = $entete;
                $message .= $resume;
                $message .= $url;

                include 'config/signat.php';

                send_email($email, $subject, $message, '', true, 'html');
            }
        }
    }

    #autodoc subscribe_query($Xuser,$Xtype, $Xclef) : Retourne true si le membre est abonné; à un topic ou forum
    function subscribe_query($Xuser, $Xtype, $Xclef)
    {
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
