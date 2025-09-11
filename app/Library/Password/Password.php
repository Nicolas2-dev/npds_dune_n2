<?php

namespace App\Library\Password;


class Password
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
     * Calcule le coût optimal pour le hashage d'un mot de passe avec Bcrypt ou autre algorithme compatible.
     *
     * Cette méthode teste les coûts de 8 à 12 et retourne le premier coût dont
     * le temps de calcul dépasse le temps minimum alloué.
     *
     * @param string $pass Mot de passe à hasher.
     * @param int $algoCrypt Constante de l'algorithme de hashage (ex: PASSWORD_BCRYPT).
     * @param int $min_ms Temps minimum en millisecondes pour le calcul du hash (par défaut 100 ms).
     * @return int|null Coût optimal détecté, ou null si aucun coût ne dépasse le temps minimum.
     */
    public function getOptimalBcryptCostParameter(string $pass, int $algoCrypt, int $min_ms = 100): ?int
    {
        for ($i = 8; $i < 13; $i++) {

            $calculCost = [
                'cost' => $i
            ];
            
            $time_start = microtime(true);

            password_hash($pass, $algoCrypt, $calculCost);

            $time_end = microtime(true);

            if (($time_end - $time_start) * 1000 > $min_ms) {
                return $i;
            }
        }

        return null;
    }
    
}

