<?php

namespace App\Library\Pollbooth;


class Pollbooth
{

    #autodoc pollSecur($pollID) : Assure la gestion des sondages membres
    function pollSecur($pollID)
    {
        global $user;

        //$pollIDX = false;

        $pollClose = '';

        $result = sql_query("SELECT pollType 
                            FROM " . sql_prefix('poll_data') . " 
                            WHERE pollID='$pollID'");

        if (sql_num_rows($result)) {
            list($pollType) = sql_fetch_row($result);

            $pollClose = (($pollType / 128) >= 1 ? 1 : 0);
            $pollType = $pollType % 128;

            if (($pollType == 1) and !isset($user)) {
                $pollClose = 99;
            }
        }

        return array($pollID, $pollClose);
    }

    #autodoc PollNewest() : Bloc Sondage <br />=> syntaxe : <br />function#pollnewest<br />params#ID_du_sondage OU vide (dernier sondage créé)
    function PollNewest(?int $id = null): void
    {
        // snipe : multi-poll evolution
        if ($id != 0) {
            settype($id, 'integer');

            list($ibid, $pollClose) = pollSecur($id);

            if ($ibid) {
                pollMain($ibid, $pollClose);
            }
        } elseif ($result = sql_query("SELECT pollID 
                                    FROM " . sql_prefix('poll_data') . " 
                                    ORDER BY pollID DESC 
                                    LIMIT 1")) {

            list($pollID) = sql_fetch_row($result);

            list($ibid, $pollClose) = pollSecur($pollID);

            if ($ibid) {
                pollMain($ibid, $pollClose);
            }
        }
}

}
