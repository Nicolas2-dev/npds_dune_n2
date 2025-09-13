<?php

namespace Npds\Support;

class Str
{

    /**
     * Cache pour les conversions camelCase
     *
     * @var array<string, string>
     */
    protected static array $camelCache = [];

    /**
     * Cache pour les conversions StudlyCase
     *
     * @var array<string, string>
     */
    protected static array $studlyCache = [];


    /**
     * Convertit une chaîne en camelCase.
     *
     * @param string $value Chaîne à convertir
     * @return string Chaîne convertie en camelCase
     */
    public static function camel(string $value): string
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    /**
     * Détermine si la chaîne donnée contient au moins une des sous-chaînes spécifiées.
     *
     * @param string          $haystack La chaîne dans laquelle rechercher.
     * @param string|string[] $needles  La ou les sous-chaînes à chercher.
     *
     * @return bool True si au moins une des sous-chaînes est trouvée, sinon false.
     */
    public static function contains(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if (($needle != '') && (mb_strpos($haystack, $needle) !== false)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si une chaîne se termine par une ou plusieurs sous-chaînes.
     *
     * @param string $haystack Chaîne à tester
     * @param string|array $needles Sous-chaîne(s) recherchée(s)
     * @return bool
     */
    public static function endsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si une chaîne commence par une ou plusieurs sous-chaînes.
     *
     * @param string $haystack Chaîne à tester
     * @param string|array $needles Sous-chaîne(s) recherchée(s)
     * @return bool
     */
    public static function startsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if (($needle != '') && (substr($haystack, 0, strlen($needle)) === (string) $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convertit une chaîne en StudlyCase.
     *
     * @param string $value Chaîne à convertir
     * @return string Chaîne convertie en StudlyCase
     */
    public static function studly(string $value): string
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

}
