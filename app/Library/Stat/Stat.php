<?php

namespace App\Library\Stat;


class Stat
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
     * Retourne un tableau contenant les statistiques du site (membres, news, critiques, forums, sujets, pages vues).
     *
     * @return array<int> Tableau contenant les statistiques dans l'ordre suivant :
     *  [0] => nombre de membres
     *  [1] => nombre de news
     *  [2] => nombre de critiques
     *  [3] => nombre de forums
     *  [4] => nombre de sujets
     *  [5] => nombre total de pages vues
     */
    public function reqStat(): array
    {
        // Les membres
        $result = sql_query("SELECT uid 
                            FROM " . sql_prefix('users'));

        $xtab[0] = $result ? (sql_num_rows($result) - 1) : '0';

        if ($result) {
            sql_free_result($result);
        }

        // Les Nouvelles (News)
        $result = sql_query("SELECT sid FROM " . sql_prefix('stories'));
        $xtab[1] = $result ? sql_num_rows($result) : '0';

        if ($result) {
            sql_free_result($result);
        }

        // Les Critiques (Reviews))
        $result = sql_query("SELECT id 
                            FROM " . sql_prefix('reviews'));

        $xtab[2] = $result ? sql_num_rows($result) : '0';

        if ($result) {
            sql_free_result($result);
        }

        // Les Forums
        $result = sql_query("SELECT forum_id 
                            FROM " . sql_prefix('forums'));

        $xtab[3] = $result ? sql_num_rows($result) : '0';

        if ($result) {
            sql_free_result($result);
        }

        // Les Sujets (topics)
        $result = sql_query("SELECT topicid 
                            FROM " . sql_prefix('topics'));

        $xtab[4] = $result ? sql_num_rows($result) : '0';

        if ($result) {
            sql_free_result($result);
        }

        // Nombre de pages vues
        $result = sql_query("SELECT count 
                            FROM " . sql_prefix('counter') . " 
                            WHERE type='total'");
        list($totalz) = sql_fetch_row($result);

        $xtab[5] = $totalz++;

        sql_free_result($result);

        return $xtab;
    }
}
