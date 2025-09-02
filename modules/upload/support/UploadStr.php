<?php

namespace Modules\Upload\Support;


class UploadStr
{

    /**
     * Vérifie et ajuste les dimensions d'une image pour l'affichage.
     *
     * @param array{0:int,1:int} $size Tableau [largeur, hauteur]
     * @return string Attributs HTML width et height (ex : 'width="200" height="150"')
     */
    public static function verifSize(array $size): string
    {
        $width_max = 500;
        $height_max = 500;

        if ($size[0] == 0) {
            $size[0] = ceil($width_max / 3);
        }

        if ($size[1] == 0) {
            $size[1] = ceil($height_max / 3);
        }

        $width = $size[0];
        $height = $size[1];

        if ($width > $width_max) {
            $imageProp = ($width_max * 100) / $width;
            $height = ceil(($height * $imageProp) / 100);
            $width = $width_max;
        }

        if ($height > $height_max) {
            $imageProp = ($height_max * 100) / $height;
            $width = ceil(($width * $imageProp) / 100);
            $height = $height_max;
        }

        return ('width="' . $width . '" height="' . $height . '"');
    }

    /**
     * Applique un word wrap sur une chaîne de texte.
     *
     * @param string $string Texte à découper
     * @param int $cols Nombre de colonnes
     * @param string $prefix Préfixe pour chaque ligne
     * @return string Texte formaté avec retours à la ligne
     */
    public static function wordWrap(string $string, int $cols = 80, string $prefix = ''): string
    {
        $t_lines = explode("\n", $string);

        $outlines = '';

        foreach ($t_lines as $thisline) {
            if (strlen($thisline) > $cols) {

                $newline = '';
                $t_l_lines = explode(' ', $thisline);

                foreach ($t_l_lines as $thisword) {
                    while ((strlen($thisword) + strlen($prefix)) > $cols) {
                        $cur_pos = 0;
                        $outlines .= $prefix;

                        for ($num = 0; $num < $cols - 1; $num++) {
                            $outlines .= $thisword[$num];
                            $cur_pos++;
                        }

                        $outlines .= "\n";
                        $thisword = substr($thisword, $cur_pos, (strlen($thisword) - $cur_pos));
                    }

                    if ((strlen($newline) + strlen($thisword)) > $cols) {
                        $outlines .= $prefix . $newline . "\n";
                        $newline = $thisword . ' ';
                    } else {
                        $newline .= $thisword . ' ';
                    }
                }
                $outlines .= $prefix . $newline . "\n";
            } else {
                $outlines .= $prefix . $thisline . "\n";
            }
        }

        return $outlines;
    }

    /**
     * Échappe le HTML pour affichage source.
     *
     * @param string $text Texte HTML
     * @return string Texte échappé
     */
    public static function scrHtml(string $text): string
    {
        return str_replace(
            ['<', '>'], 
            ['&lt;', '&gt;'], 
            $text
        );
    }
    
}
