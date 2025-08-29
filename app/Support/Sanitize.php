<?php

namespace App\Support;


class Sanitize
{

    /**
     * Convertit les retours chariot (\r\n, \r, \n) et les balises <BR> en <br /> XHTML.
     *
     * @param string $txt Texte à convertir.
     * @return string Texte converti avec des <br />.
     */
    public static function conv2br(string $txt): string
    {
        $Xcontent = str_replace("\r\n", '<br />', $txt);
        $Xcontent = str_replace("\r", '<br />', $Xcontent);
        $Xcontent = str_replace("\n", '<br />', $Xcontent);
        $Xcontent = str_replace('<BR />', '<br />', $Xcontent);
        $Xcontent = str_replace('<BR>', '<br />', $Xcontent);

        return $Xcontent;
    }

    /**
     * Génère une valeur hexadécimale unique à partir des 8 premiers caractères du MD5 d'une chaîne.
     *
     * @param string $txt Chaîne à convertir.
     * @return int Valeur hexadécimale mod 16.
     */
    public static function hexfromchr(string $txt): int
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
    public static function changetoamp(array $r): string
    {
        return str_replace('&', '&amp;', $r[0]);
    } 

    /**
     * Idem changetoamp, mais pour l'administration.
     *
     * @param array $r Tableau contenant la chaîne à transformer.
     * @return string Chaîne avec & remplacé.
     */
    public static function changetoampadm(array $r): string
    {
        return static::changetoamp($r);
    }

    /**
     * Encode une chaîne UTF-8 pour JavaScript.
     *
     * Convertit les entités HTML numériques en \uXXXX JavaScript.
     *
     * @param string $ibid Chaîne UTF-8.
     * @return string Chaîne encodée pour JavaScript.
     */
    public static function utf8_java(string $ibid): string
    {
        // UTF8 = &#x4EB4;&#x6B63;&#7578; / javascript = \u4EB4\u6B63\u.dechex(7578)
        $tmp = explode('&#', $ibid);

        foreach ($tmp as $bidon) {
            if ($bidon) {
                $bidon = substr($bidon, 0, strpos($bidon, ';'));
                $hex = strpos($bidon, 'x');

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
        $tmp = str_replace(' ', '&nbsp;', $tmp);

        return $tmp;
    }

    /**
     * Découpe une chaîne en morceaux de longueur $split si elle ne contient pas d'espace.
     *
     * @param string $msg Chaîne à découper.
     * @param int $split Longueur maximale des morceaux.
     * @return string Chaîne modifiée.
     */
    public static function split_string_without_space(string $msg, int $split): string
    {
        $Xmsg = explode(' ', $msg);
        array_walk($Xmsg, [self::class, 'wrapper_f'], $split);
        $Xmsg = implode(' ', $Xmsg);

        return $Xmsg;
    }

    /**
     * Fonction wrapper utilisée par split_string_without_space.
     *
     * @param string $string Chaîne passée par référence.
     * @param int $key Clé de l'élément (non utilisée).
     * @param int $cols Longueur maximale d'une portion.
     * @return void
     */
    public static function wrapper_f(string &$string, int $key, int $cols): void
    {
        //   if (!(stristr($string,'IMG src=') or stristr($string,'A href=') or stristr($string,'HTTP:') or stristr($string,'HTTPS:') or stristr($string,'MAILTO:') or stristr($string,'[CODE]'))) {
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
        //   }
    }

    /**
     * Échappe les quotes dans une chaîne pour SQL.
     *
     * @param string $what Chaîne à traiter.
     * @return string Chaîne échappée.
     */
    public static function FixQuotes(string $what = ''): string
    {
        $what = str_replace("&#39;", "'", $what);
        $what = str_replace("'", "''", $what);

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
    public static function addslashes_GPC(&$arr)
    {
        $arr = addslashes($arr);
    }

}
