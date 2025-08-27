<?php

namespace App\Library\Code;


class Code
{

    #autodoc af_cod($ibid) : Analyse le contenu d'une chaîne et converti les pseudo-balises [code]...[/code] et leur contenu en html
    function change_cod($r)
    {
        return '<' . $r[2] . ' class="language-' . $r[3] . '">' . htmlentities($r[5], ENT_COMPAT | ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8') . '</' . $r[2] . '>';
    }

    function af_cod($ibid)
    {
        $pat = '#(\[)(\w+)\s+([^\]]*)(\])(.*?)\1/\2\4#s';
        $ibid = preg_replace_callback($pat, 'change_cod', $ibid, -1, $nb);
        //   $ibid= str_replace(array("\r\n", "\r", "\n"), "<br />",$ibid);

        return $ibid;
    }

    #autodoc desaf_cod($ibid) : Analyse le contenu d'une chaîne et converti les balises html <code>...</code> en pseudo-balises [code]...[/code]
    function desaf_cod($ibid)
    {
        $pat = '#(<)(\w+)\s+(class="language-)([^">]*)(">)(.*?)\1/\2>#';

        function rechange_cod($r)
        {
            return '[' . $r[2] . ' ' . $r[4] . ']' . $r[6] . '[/' . $r[2] . ']';
        }

        $ibid = preg_replace_callback($pat, 'rechange_cod', $ibid, -1);

        return $ibid;
    }

    #autodoc aff_code($ibid) : Analyse le contenu d'une chaîne et converti les balises [code]...[/code]
    function aff_code($ibid)
    {
        $pasfin = true;

        while ($pasfin) {
            $pos_deb = strpos($ibid, '[code]', 0);
            $pos_fin = strpos($ibid, '[/code]', 0);

            // ne pas confondre la position ZERO et NON TROUVE !
            if ($pos_deb === false) {
                $pos_deb = -1;
            }

            if ($pos_fin === false) {
                $pos_fin = -1;
            }

            if (($pos_deb >= 0) and ($pos_fin >= 0)) {
                ob_start();
                    highlight_string(substr($ibid, $pos_deb + 6, ($pos_fin - $pos_deb - 6)));
                    $fragment = ob_get_contents();
                ob_end_clean();

                $ibid = str_replace(substr($ibid, $pos_deb, ($pos_fin - $pos_deb + 7)), $fragment, $ibid);
            } else {
                $pasfin = false;
            }
        }

        return $ibid;
    }

}
