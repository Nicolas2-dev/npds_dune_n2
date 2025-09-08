<?php

use App\Library\auth\Auth;
use App\Library\Theme\Theme;
use App\Library\Groupe\Groupe;

if (! function_exists('bloc_espace_groupe')) {
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

        Theme::themeSidebox($title, Groupe::fabEspaceEroupe($gr, "0", $i_gr));
    }
}

if (! function_exists('bloc_groupes')) {
    #autodoc bloc_groupes() : Bloc des groupes <br />=> syntaxe :<br />function#bloc_groupes<br />params#Aff_img_groupe(0 ou 1) / Si le bloc n'a pas de titre, 'Les groupes' sera utilisé. Liste des groupes AVEC membres et lien pour demande d'adhésion pour l'utilisateur.
    function bloc_groupes($im)
    {
        global $block_title, $user;

        $title = $block_title == '' ? 'Les groupes' : $block_title;

        Theme::themeSidebox($title, fab_groupes_bloc($user, $im));
    }
}

if (! function_exists('fab_groupes_bloc')) {
    function fab_groupes_bloc($user, $im)
    {
        $user;

        $lstgr = array();

        $userdata = explode(':', base64_decode($user));
        $result = sql_query("SELECT DISTINCT groupe 
                            FROM " . sql_prefix('users_status') . " 
                            WHERE groupe > 1;");

        while (list($groupe) = sql_fetch_row($result)) {

            $pos = strpos($groupe, ',');

            if ($pos === false) {
                $lstgr[] = $groupe;
            } else {
                $arg = explode(',', $groupe);

                foreach ($arg as $v) {
                    if (!in_array($v, $lstgr, true)) {
                        $lstgr[] = $v;
                    }
                }
            }
        }

        $ids_gr = join("','", $lstgr);

        sql_free_result($result);

        $result = sql_query("SELECT groupe_id, groupe_name, groupe_description 
                            FROM " . sql_prefix('groupes') . " 
                            WHERE groupe_id IN ('$ids_gr')");

        $nb_groupes = sql_num_rows($result);

        $content = '<div id="bloc_groupes" class="">
            <ul id="lst_groupes" class="list-group list-group-flush mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                    <div class="me-auto">
                        <div class="fw-bold"><i class="fa fa-users fa-2x text-body-secondary me-2"></i>' . translate('Groupes') . '</div>';

        $content .= $nb_groupes > 0 ? translate('Groupe ouvert') : translate('Pas de groupe ouvert');

        $content .= '</div>
                <span class="badge bg-primary rounded-pill">' . $nb_groupes . '</span>
            </li>';

        while (list($groupe_id, $groupe_name, $groupe_description) = sql_fetch_row($result)) {
            $content .= '<li class="list-group-item px-0">' . $groupe_name . '<div class="small">' . $groupe_description . '</div>';

            $content .= $im == 1 ? '<div class="text-center my-2"><img class="img-fluid" src="storage/users_private/groupe/' . $groupe_id . '/groupe.png" loading="lazy"></div>' : '';

            if (!file_exists('storage/users_private/groupe/ask4group_' . $userdata[0] . '_' . $groupe_id . '_.txt') and !Auth::autorisation($groupe_id)) {
                if (!Auth::autorisation(-1)) {
                    $content .= '<div class="text-end small"><a href="user.php?op=askforgroupe&amp;askedgroup=' . $groupe_id . '" title="' . translate('Envoi une demande aux administrateurs pour rejoindre ce groupe. Un message privé vous informera du résultat de votre demande.') . '" data-bs-toggle="tooltip">' . translate('Rejoindre ce groupe') . '</a></div>';
                }
            }

            $content .= '</li>';
        }

        $content .= '</ul>';

        if (Auth::autorisation(-127)) {
            $content .= '<div class="text-end"><a class="mx-2" href="admin.php?op=groupes" ><i title="' . translate('Gestion des groupes.') . '" data-bs-toggle="tooltip" data-bs-placement="left" class="fa fa-cogs fa-lg"></i></a></div>';
        }

        $content .= '</div>';

        sql_free_result($result);

        return $content;
    }
}
