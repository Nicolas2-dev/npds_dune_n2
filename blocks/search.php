<?php

#autodoc searchbox() : Bloc Search-engine <br />=> syntaxe : function#searchbox
function searchbox()
{
   global $block_title;

   $title = $block_title == '' ? translate('Recherche') : $block_title;

   $content = '<form id="searchblock" action="search.php" method="get">
        <input class="form-control" type="text" name="query" />
    </form>';

   themesidebox($title, $content);
}
