<?php

namespace App\Library\Date;

use IntlDateFormatter;
use App\Library\Language\Language;


class Date
{

    /**
     * Détermine si c'est actuellement le jour ou la nuit.
     * Se base sur l'heure de lever et de coucher du soleil définies dans les variables globales `$lever` et `$coucher`.
     *
     * @global string $lever  Heure de lever du soleil (format accepté par strtotime)
     * @global string $coucher Heure de coucher du soleil (format accepté par strtotime)
     * @return string 'Jour' si l'heure actuelle est entre le lever et le coucher, 'Nuit' sinon
     */
    public static function nightDay(): string
    {
        global $lever, $coucher;

        $maintenant = strtotime('now');

        $jour = strtotime($lever);
        $nuit = strtotime($coucher);

        return ($maintenant - $jour < 0 xor $maintenant - $nuit > 0) ? 'Nuit' : 'Jour';
    }

    /**
     * Formate un timestamp ou une chaîne de date en fonction des styles de date/heure donnés.
     * Le décalage GMT défini dans les préférences n'est pas appliqué.
     *
     * @param int|string $time        Timestamp Unix ou chaîne de date compréhensible par strtotime
     * @param int        $dateStyle   Style de date selon IntlDateFormatter (default: IntlDateFormatter::SHORT)
     * @param int        $timeStyle   Style d'heure selon IntlDateFormatter (default: IntlDateFormatter::NONE)
     * @param string     $timezone    Fuseau horaire à utiliser (default: 'Europe/Paris')
     * @return string   Date formatée avec première lettre en majuscule et encodage HTML
     */
    public static function formatTimes(
        int|string  $time,
        int         $dateStyle = IntlDateFormatter::SHORT,
        int         $timeStyle = IntlDateFormatter::NONE,
        string      $timezone  = 'Europe/Paris'
    ): string {
        // Utilise la langue de l'affichage du site.
        $locale = Language::languageIso(1, '_', 1);

        $fmt = datefmt_create($locale, $dateStyle, $timeStyle, $timezone, IntlDateFormatter::GREGORIAN);

        $timestamp = is_numeric($time) ? (int) $time : strtotime($time);

        return ucfirst(htmlentities($fmt->format($timestamp), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'));
    }

    /**
     * Extrait et formate une partie d'un timestamp ou d'une chaîne de date.
     * Le format de sortie est défini via le paramètre $format (pattern IntlDateFormatter).
     *
     * @param int|string $time       Timestamp Unix ou chaîne de date compréhensible par strtotime
     * @param string     $format     Pattern de formatage compatible IntlDateFormatter
     * @param string     $timezone   Fuseau horaire à utiliser (default: 'Europe/Paris')
     * @return string   Date formatée avec première lettre en majuscule et encodage HTML
     */
    public static function getPartOfTime(
        int|string  $time,
        string      $format,
        string      $timezone = 'Europe/Paris'
    ): string {
        // Utilise la langue de l'affichage du site.
        $locale = Language::languageIso(1, '_', 1);

        $timestamp = is_numeric($time) ? (int) $time : strtotime($time);

        $fmt = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            $timezone,
            IntlDateFormatter::GREGORIAN,
            $format
        );

        $dateFormat = $fmt->format($timestamp);

        return ucfirst(htmlentities($dateFormat, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'));
    }
}
