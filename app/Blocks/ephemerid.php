<?php

if (! function_exists('ephemblock'))
{ 
    #autodoc ephemblock() : Bloc ephemerid <br />=> syntaxe : function#ephemblock
    function ephemblock()
    {
        global $gmt;

        $cnt = 0;

        $eday = date("d", time() + ((int)$gmt * 3600));
        $emonth = date("m", time() + ((int)$gmt * 3600));

        $result = sql_query("SELECT yid, content 
                            FROM " . sql_prefix('ephem') . " 
                            WHERE did='$eday' 
                            AND mid='$emonth' 
                            ORDER BY yid ASC");

        $boxstuff = '<div>' . translate('En ce jour...') . '</div>';

        while (list($yid, $content) = sql_fetch_row($result)) {
            if ($cnt == 1) {
                $boxstuff .= "\n<br />\n";
            }

            $boxstuff .= "<b>$yid</b>\n<br />\n";
            $boxstuff .= aff_langue($content);

            $cnt = 1;
        }

        $boxstuff .= "<br />\n";

        global $block_title;
        $title = $block_title == '' ? translate('Ephémérides') : $block_title;

        themesidebox($title, $boxstuff);
    }
}
