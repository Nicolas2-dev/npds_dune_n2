<?php

namespace Npds\Config;

use Npds\Support\Arr;

class Config 
{

    /**
     * Options de configuration stockées en mémoire.
     *
     * @var array
     */
    protected static array $options = [];


    /**
     * Retourne toutes les options de configuration.
     *
     * @return array Toutes les options
     */
    public static function all(): array
    {
        return static::$options;
    }

    /**
     * Retourne les options d'un item spécifique.
     *
     * @param string $item Nom de l'item
     *
     * @return mixed Valeur de l'item demandé
     */
    public static function items(string $item): mixed
    {
        return static::$options[$item] ?? null;
    }

    /**
     * Vérifie si une clé existe dans les options.
     *
     * @param string $key Clé à vérifier
     *
     * @return bool True si la clé existe, false sinon
     */
    public static function has(string $key): bool
    {
        return Arr::has(static::$options, $key);
    }

    /**
     * Récupère la valeur d'une clé dans les options.
     *
     * @param string $key     Clé à récupérer
     * @param mixed  $default Valeur par défaut si la clé n'existe pas
     *
     * @return mixed Valeur de la clé ou valeur par défaut
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Arr::get(static::$options, $key, $default);
    }

    /**
     * Définit une valeur pour une clé dans les options.
     *
     * @param string $key   Clé à définir
     * @param mixed  $value Valeur à assigner
     *
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        Arr::set(static::$options, $key, $value);
    }
    
}
