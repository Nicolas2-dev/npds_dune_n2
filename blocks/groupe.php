<?php

if (! function_exists('bloc_espace_groupe'))
{ 
    #autodoc espace_groupe() : Bloc du WorkSpace <br />=> syntaxe :<br />function#bloc_espace_groupe<br />params#ID_du_groupe, Aff_img_groupe(0 ou 1) / Si le bloc n'a pas de titre, Le nom du groupe sera utilisé
    function bloc_espace_groupe($gr, $i_gr)
    {
        global $block_title;

        if ($block_title == '') {
            $rsql = sql_fetch_assoc(sql_query("SELECT groupe_name 
                                            FROM " . sql_prefix('groupes') . " 
                                            WHERE groupe_id='$gr'"));
                                            
            $title = $rsql['groupe_name'];
        } else {
            $title = $block_title;
        }

        themesidebox($title, fab_espace_groupe($gr, "0", $i_gr));
    }
}

if (! function_exists('bloc_groupes'))
{ 
    #autodoc bloc_groupes() : Bloc des groupes <br />=> syntaxe :<br />function#bloc_groupes<br />params#Aff_img_groupe(0 ou 1) / Si le bloc n'a pas de titre, 'Les groupes' sera utilisé. Liste des groupes AVEC membres et lien pour demande d'adhésion pour l'utilisateur.
    function bloc_groupes($im)
    {
        global $block_title, $user;

        $title = $block_title == '' ? 'Les groupes' : $block_title;

        themesidebox($title, fab_groupes_bloc($user, $im));
    }
}
