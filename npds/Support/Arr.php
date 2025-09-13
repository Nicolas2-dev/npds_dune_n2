<?php

namespace Npds\Support;

class Arr 
{
    
    /**
     * Supprime une ou plusieurs clés d'un tableau.
     *
     * @param array $array Tableau source
     * @param string|array $keys Clé(s) à exclure
     * @return array Tableau après suppression des clés
     */
    public static function except(array $array, string|array $keys): array
    {
        return array_diff_key($array, array_flip((array) $keys));
    }

    /**
     * Retourne le premier élément d'un tableau qui satisfait une condition.
     *
     * @param array $array Tableau à parcourir
     * @param callable $callback Fonction de test (clé, valeur)
     * @param mixed $default Valeur par défaut si aucun élément trouvé
     * @return mixed
     */
    public static function first(array $array, callable $callback, mixed $default = null): mixed
    {
        foreach ($array as $key => $value)
        {
            if (call_user_func($callback, $key, $value)) return $value;
        }

        return value($default);
    }

    /**
     * Récupère une valeur d'un tableau via une clé en notation "dot".
     *
     * @param array $array Tableau source
     * @param string|null $key Clé à récupérer (ex: "user.name")
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed
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
     * Vérifie si une clé existe dans un tableau (support "dot").
     *
     * @param array $array Tableau source
     * @param string|null $key Clé à vérifier
     * @return bool
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
     * Définit une valeur dans un tableau via une clé en notation "dot".
     *
     * @param array $array Tableau à modifier (passé par référence)
     * @param string $key Clé à définir (ex: "user.name")
     * @param mixed $value Valeur à définir
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
