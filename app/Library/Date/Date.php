<?php

namespace App\Library\Date;


class Cookie
{

    #autodoc NightDay() : Pour obtenir Nuit ou Jour ... Un grand Merci à P.PECHARD pour cette fonction
    function NightDay()
    {
        global $lever, $coucher;

        $Maintenant = strtotime('now');

        $Jour = strtotime($lever);
        $Nuit = strtotime($coucher);

        if ($Maintenant - $Jour < 0 xor $Maintenant - $Nuit > 0) {
            return 'Nuit';
        } else {
            return 'Jour';
        }
    }
    
    #autodoc formatTimes($time) : Formate un timestamp ou une chaine de date formatée correspondant à l'argument obligatoire $time - le décalage $gmt défini dans les préférences n'est pas appliqué
    function formatTimes($time, $dateStyle = IntlDateFormatter::SHORT, $timeStyle = IntlDateFormatter::NONE, $timezone = 'Europe/Paris')
    {
        $locale = language_iso(1, '_', 1); // Utilise la langue de l'affichage du site
        $fmt = datefmt_create($locale, $dateStyle, $timeStyle, $timezone, IntlDateFormatter::GREGORIAN);

        $timestamp = is_numeric($time) ? $time : strtotime($time);
        $date_au_format = ucfirst(htmlentities($fmt->format($timestamp), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'));

        return $date_au_format;
    }

    #autodoc getPartOfTime($time) : découpe/extrait/formate et plus grâce au paramètre $format.... un timestamp ou une chaine de date formatée correspondant à l'argument obligatoire $time -
    function getPartOfTime($time, $format, $timezone = 'Europe/Paris')
    {
        $locale = language_iso(1, '_', 1);
        $timestamp = is_numeric($time) ? $time : strtotime($time);

        $fmt = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::FULL, $timezone, IntlDateFormatter::GREGORIAN, $format);
        $date_au_format = $fmt->format($timestamp);

        return ucfirst(htmlentities($date_au_format, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'));
    }

}
