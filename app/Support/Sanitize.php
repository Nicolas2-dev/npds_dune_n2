<?php

namespace App\Support;


class Sanitize
{

    #autodoc conv2br($txt) : convertie \r \n  BR ... en br XHTML
    function conv2br($txt)
    {
        $Xcontent = str_replace("\r\n", '<br />', $txt);
        $Xcontent = str_replace("\r", '<br />', $Xcontent);
        $Xcontent = str_replace("\n", '<br />', $Xcontent);
        $Xcontent = str_replace('<BR />', '<br />', $Xcontent);
        $Xcontent = str_replace('<BR>', '<br />', $Xcontent);

        return $Xcontent;
    }

    #autodoc hexfromchr($txt) : Les 8 premiers caractères sont convertis en UNE valeur Hexa unique 
    function hexfromchr($txt)
    {
        $surlignage = substr(md5($txt), 0, 8);
        $tmp = 0;

        for ($ix = 0; $ix <= 5; $ix++) {
            $tmp += hexdec($surlignage[$ix]) + 1;
        }

        return $tmp %= 16;
    }

    function changetoamp($r)
    {
        return str_replace('&', '&amp;', $r[0]);
    } //must work from php 4 to 7 !..?..

    function changetoampadm($r)
    {
        return str_replace('&', '&amp;', $r[0]);
    }

    #autodoc utf8_java($ibid) : Encode une chaine UF8 au format javascript - JPB 2005
    function utf8_java($ibid)
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

    #autodoc wrh($ibid) : Formate une chaine numérique avec un espace tous les 3 chiffres / cheekybilly 2005
    function wrh($ibid)
    {
        $tmp = number_format($ibid, 0, ',', ' ');
        $tmp = str_replace(' ', '&nbsp;', $tmp);

        return $tmp;
    }

    #####// ces deux fonctions suivantes génèrent des erreurs multiples à corriger ou supprimer Warning: Uninitialized string offset 16 in mainfile.php on line 1655
    #autodoc split_string_without_space($msg, $split) : Découpe la chaine en morceau de $slpit longueur si celle-ci ne contient pas d'espace / Snipe 2004
    function split_string_without_space($msg, $split)
    {
        $Xmsg = explode(' ', $msg);
        array_walk($Xmsg, 'wrapper_f', $split);
        $Xmsg = implode(' ', $Xmsg);

        return $Xmsg;
    }

    #autodoc wrapper_f (&$string, $key, $cols) : Fonction Wrapper pour split_string_without_space / Snipe 2004
    function wrapper_f(&$string, $key, $cols)
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

    #autodoc FixQuotes($what) : Quote une chaîne contenant des '
    function FixQuotes($what = '')
    {
        $what = str_replace("&#39;", "'", $what);
        $what = str_replace("'", "''", $what);

        while (preg_match("#\\\\'#", $what)) {
            $what = preg_replace("#\\\\'#", "'", $what);
        }

        return $what;
    }

    function addslashes_GPC(&$arr)
    {
        $arr = addslashes($arr);
    }

}
