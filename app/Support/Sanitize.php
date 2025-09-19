<?php

namespace App\Support;

use App\Support\Security\Hack;


class Sanitize
{

    /**
     * Convertit les retours chariot (\r\n, \r, \n) et les balises <BR> en <br /> XHTML.
     *
     * @param string $txt Texte à convertir.
     * @return string Texte converti avec des <br />.
     */
    public static function convertToBr(string $txt): string
    {
        return str_replace(
            ["\r\n", "\r", "\n", '<BR />', '<BR>'],
            '<br />',
            $txt
        );
    }

    /**
     * Filtre un argument passé aux requêtes SQL.
     * Cette fonction est automatiquement appelée par META-LANG lors du passage de paramètres.
     *
     * @param string $arg L'argument à filtrer.
     * @return string L'argument filtré.
     */
    public static function argFilter(string $arg): string
    {
        return Hack::removeHack(stripslashes(htmlspecialchars(urldecode($arg), ENT_QUOTES, 'UTF-8')));
    }

    /**
     * Génère une valeur hexadécimale unique à partir des 8 premiers caractères du MD5 d'une chaîne.
     *
     * @param string $txt Chaîne à convertir.
     * @return int Valeur hexadécimale mod 16.
     */
    public static function hexFromChr(string $txt): int
    {
        $surlignage = substr(md5($txt), 0, 8);

        $tmp = 0;

        for ($ix = 0; $ix <= 5; $ix++) {
            $tmp += hexdec($surlignage[$ix]) + 1;
        }

        return $tmp %= 16;
    }

    /**
     * Remplace le caractère & par &amp;.
     *
     * @param array $r Tableau contenant la chaîne à transformer.
     * @return string Chaîne avec & remplacé.
     */
    public static function changeToAmp(array $r): string
    {
        return str_replace('&', '&amp;', $r[0]);
    }

    /**
     * Encode une chaîne UTF-8 pour JavaScript.
     *
     * Convertit les entités HTML numériques en \uXXXX JavaScript.
     *
     * @param string $ibid Chaîne UTF-8.
     * @return string Chaîne encodée pour JavaScript.
     */
    public static function utf8Java(string $ibid): string
    {
        // UTF8 = &#x4EB4;&#x6B63;&#7578; 
        // javascript = \u4EB4\u6B63\u.dechex(7578)

        foreach (explode('&#', $ibid) as $bidon) {
            if ($bidon) {
                $bidon  = substr($bidon, 0, strpos($bidon, ';'));
                $hex    = strpos($bidon, 'x');

                $ibid = ($hex === false)
                    ? str_replace('&#' . $bidon . ';', '\\u' . dechex((int)$bidon), $ibid)
                    : str_replace('&#' . $bidon . ';', '\\u' . substr($bidon, 1), $ibid);
            }
        }

        return $ibid;
    }

    /**
     * Formate un nombre en ajoutant un espace tous les 3 chiffres (ou &nbsp;).
     *
     * @param int|float $ibid Nombre à formater.
     * @return string Nombre formaté.
     */
    public static function wrh($ibid): string
    {
        $tmp = number_format($ibid, 0, ',', ' ');

        return str_replace(' ', '&nbsp;', $tmp);
    }

    /**
     * Découpe une chaîne en morceaux de longueur $split si elle ne contient pas d'espace.
     *
     * @param string $msg Chaîne à découper.
     * @param int $split Longueur maximale des morceaux.
     * @return string Chaîne modifiée.
     */
    public static function splitStringWithoutSpace(string $msg, int $split): string
    {
        $Xmsg = explode(' ', $msg);
        array_walk($Xmsg, [static::class, 'wrapperF'], $split);
        $Xmsg = implode(' ', $Xmsg);

        return $Xmsg;
    }

    /**
     * Fonction wrapper utilisée par split_string_without_space.
     *
     * @param string $string Chaîne passée par référence.
     * @param int $key obligatoire pour array_walk.
     * @param int $cols Longueur maximale d'une portion.
     * @return void
     */
    public static function wrapperF(string &$string, int $key, int $cols): void
    {
        // if (!(stristr($string, 'IMG src=') 
        // || stristr($string, 'A href=') 
        // || stristr($string, 'HTTP:') 
        // || stristr($string, 'HTTPS:') 
        // || stristr($string, 'MAILTO:') 
        // || stristr($string, '[CODE]'))) {
        $outlines = '';

        if (strlen($string) > $cols) {
            while (strlen($string) > $cols) {
                $cur_pos = 0;

                for ($num = 0; $num < $cols - 1; $num++) {
                    $outlines .= $string[$num];
                    $cur_pos++;

                    if ($string[$num] == "\n") {
                        $string = substr($string, $cur_pos, (strlen($string) - $cur_pos));
                        $cur_pos = 0;
                        $num = 0;
                    }
                }

                $outlines .= '<i class="fa fa-cut fa-lg"> </i>';
                $string = substr($string, $cur_pos, (strlen($string) - $cur_pos));
            }

            $string = $outlines . $string;
        }
        // }
    }

    /**
     * Divise les mots d'une chaîne trop longs en morceaux de taille maximale spécifiée,
     * en ajoutant un indicateur visuel de coupure pour les mots dépassant la limite.
     *
     * Cette version est prévue pour des tests/development.
     *
     * @param string $msg   La chaîne à traiter, où chaque mot séparé par un espace sera examiné.
     * @param int    $split La longueur maximale autorisée pour chaque mot avant insertion d'un marqueur.
     *
     * @return string La chaîne traitée avec les mots longs coupés et séparés par des espaces.
     *
     * @example
     * $text = "BonjourSuperLongMotIninterrompu Test";
     * echo splitStringWithoutSpace_devTest($text, 10);
     * // Résultat possible : "BonjourSu<i class="fa fa-cut fa-lg"> </i>perLongMotIninterrompu Test"
     */
    public static function splitStringWithoutSpace_devTest(string $msg, int $split): string
    {
        return implode(' ', array_map(
            fn($word) => static::wrapperF_devTest($word, $split),
            explode(' ', $msg)
        ));
    }

    /**
     * Fonction wrapper utilisée par split_string_without_space.
     *
     * @param string $string Chaîne passée par référence.
     * @param int $cols Longueur maximale d'une portion.
     * @return void
     */
    public static function wrapperF_devTest(string $string, int $cols): void
    {
        $outlines = '';

        if (strlen($string) > $cols) {
            while (strlen($string) > $cols) {
                $cur_pos = 0;

                for ($num = 0; $num < $cols - 1; $num++) {
                    $outlines .= $string[$num];
                    $cur_pos++;

                    if ($string[$num] == "\n") {
                        $string = substr($string, $cur_pos);
                        $cur_pos = 0;
                        $num = -1;
                    }
                }

                $outlines .= '<i class="fa fa-cut fa-lg"> </i>';
                $string = substr($string, $cur_pos);
            }

            $string = $outlines . $string;
        }
    }

    /**
     * Échappe les quotes dans une chaîne pour SQL.
     *
     * @param string $what Chaîne à traiter.
     * @return string Chaîne échappée.
     */
    public static function fixQuotes(string $what = ''): string
    {
        $what = str_replace(
            ["&#39;", "'"], 
            ["'", "''"], 
            $what
        );

        while (preg_match("#\\\\'#", $what)) {
            $what = preg_replace("#\\\\'#", "'", $what);
        }

        return $what;
    }

    /**
     * Applique addslashes à une variable passée par référence.
     *
     * @param mixed $arr Variable à échapper.
     * @return void
     */
    public static function addslashesGpc(&$arr)
    {
        $arr = addslashes($arr);
    }
}
