<?php

namespace App\Library\Pollbooth;


class Pollbooth
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;


    /**
     * Retourne l'instance singleton du dispatcher.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }
    
    /**
     * Assure la gestion sécurisée des sondages pour les membres.
     *
     * Vérifie le type de sondage et si l'utilisateur est autorisé à y accéder.
     *
     * @param int|string $pollID Identifiant du sondage.
     * @return array{0: int|string, 1: int} Tableau contenant :
     *   - [0] : l'identifiant du sondage.
     *   - [1] : l'état de fermeture du sondage :
     *       0 => sondage ouvert
     *       1 => sondage fermé
     *       99 => sondage réservé aux membres non connectés
     */
    public function pollSecur(int|string $pollID): array
    {
        global $user; // global a revoir !

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

        return [$pollID, $pollClose];
    }

}
