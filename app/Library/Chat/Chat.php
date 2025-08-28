<?php

namespace App\Library\Chat;


class Chat
{

    #autodoc if_chat() : Retourne le nombre de connecté au Chat
    function if_chat($pour)
    {
        $auto = autorisation_block('params#' . $pour);
        $dimauto = count($auto);
        $numofchatters = 0;

        if ($dimauto <= 1) {
            $result = sql_query("SELECT DISTINCT ip 
                                FROM " . sql_prefix('chatbox') . " 
                                WHERE id='" . $auto[0] . "' 
                                AND date >= " . (time() - (60 * 3)) . "");

            $numofchatters = sql_num_rows($result);
        }

        return $numofchatters;
    }

    #autodoc insertChat($username, $message, $dbname, $id) : Insère un record dans la table Chat / on utilise id pour filtrer les messages - id = l'id du groupe
    function insertChat($username, $message, $dbname, $id)
    {
        if ($message != '') {
            $username = removeHack(stripslashes(FixQuotes(strip_tags(trim($username)))));
            $message =  removeHack(stripslashes(FixQuotes(strip_tags(trim($message)))));

            $ip = getip();

            settype($id, 'integer');
            settype($dbname, 'integer');

            $result = sql_query("INSERT INTO " . sql_prefix('chatbox') . " 
                                VALUES ('" . $username . "', '" . $ip . "', '" . $message . "', '" . time() . "', '$id', " . $dbname . ")");
        }
    }

}
