<?php

if (! function_exists('mainblock'))
{ 
    #autodoc mainblock() : Bloc principal <br />=> syntaxe : function#mainblock
    function mainblock()
    {
        $result = sql_query("SELECT title, content 
                            FROM " . sql_prefix('block') . " 
                            WHERE id=1");
                            
        list($title, $content) = sql_fetch_row($result);

        global $block_title;
        if ($title == '') {
            $title = $block_title;
        }

        //must work from php 4 to 7 !..?..
        themesidebox(aff_langue($title), aff_langue(preg_replace_callback('#<a href=[^>]*(&)[^>]*>#', 'changetoamp', $content)));
    }
}
