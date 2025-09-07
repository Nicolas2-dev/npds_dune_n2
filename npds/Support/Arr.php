<?php

namespace Npds\Support;

class Arr 
{
    
    /**
     * Récupère une valeur dans un tableau en utilisant une clé "dot notation".
     *
     * @param array       $array   Tableau à parcourir
     * @param string|null $key     Clé à récupérer, peut être en notation "dot"
     * @param mixed       $default Valeur par défaut si la clé n'existe pas
     *
     * @return mixed La valeur trouvée ou la valeur par défaut
     */
    public static function get(array $array, ?string $key, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $array;
        } else if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (! is_array($array) || ! array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Vérifie si une clé existe dans un tableau, avec support de la "dot notation".
     *
     * @param array       $array Tableau à parcourir
     * @param string|null $key   Clé à vérifier
     *
     * @return bool True si la clé existe, false sinon
     */
    public static function has(array $array, ?string $key): bool
    {
        if (empty($array) || is_null($key)) {
            return false;
        } else if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (! is_array($array) || ! array_key_exists($segment, $array)) {
                return false;
            }

            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Définit une valeur dans un tableau en utilisant une clé "dot notation".
     *
     * @param array  $array Tableau à modifier (passé par référence)
     * @param string $key   Clé à définir
     * @param mixed  $value Valeur à assigner
     *
     * @return array Tableau modifié
     */
    public static function set(array &$array, string $key, mixed $value): array
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $key = array_shift($keys);

        $array[$key] = $value;

        return $array;
    }

}
