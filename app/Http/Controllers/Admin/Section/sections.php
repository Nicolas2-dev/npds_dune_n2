<?php

namespace App\Http\Controllers\Admin\;


use App\Http\Controllers\Core\AdminBaseController;


class extends AdminBaseController
{
    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        $f_meta_nom = 'sections';
        $f_titre = adm_translate('Rubriques');

        // controle droit
        admindroits($aid, $f_meta_nom);

        global $language;
        $hlpfile = 'admin/manuels/' . $language . '/sections.html';

        parent::initialize();        
    }



function groupe($groupe)
{
    $les_groupes = explode(',', $groupe);
    $mX = Groupe::listeGroup();

    $nbg = 0;
    $str = '';

    foreach ($mX as $groupe_id => $groupe_name) {
        $selectionne = 0;

        if ($les_groupes) {
            foreach ($les_groupes as $groupevalue) {
                if (($groupe_id == $groupevalue) and ($groupe_id != 0)) {
                    $selectionne = 1;
                }
            }
        }

        $str .= $selectionne == 1
            ? '<option value="' . $groupe_id . '" selected="selected">' . $groupe_name . '</option>'
            : '<option value="' . $groupe_id . '">' . $groupe_name . '</option>';
        $nbg++;
    }

    if ($nbg > 5) {
        $nbg = 5;
    }

    return ('<select class="form-control" name="Mmembers[]" multiple size="' . $nbg . '">' . $str . '</select>');
}

function droits($member)
{
    echo '<fieldset>
    <legend>' . adm_translate('Droits') . '</legend>
    <div class="mb-3">
        <div class="form-check form-check-inline">';

    if ($member == -127) {
        $checked = ' checked="checked"';
    } else {
        $checked = '';
    }

    echo '<input class="form-check-input" type="radio" id="adm" name="members" value="-127" ' . $checked . ' />
            <label class="form-check-label" for="adm">' . adm_translate('Administrateurs') . '</label>
        </div>
        <div class="form-check form-check-inline">';

    if ($member == -1) {
        $checked = ' checked="checked"';
    } else {
        $checked = '';
    }

    echo '<input class="form-check-input" type="radio" id="ano" name="members" value="-1" ' . $checked . ' />
            <label class="form-check-label" for="ano">' . adm_translate('Anonymes') . '</label>
        </div>';

    echo '<div class="form-check form-check-inline">';

    if ($member > 0) {
        echo '<input class="form-check-input" type="radio" id="mem" name="members" value="1" checked="checked" />
                <label class="form-check-label" for="mem">' . adm_translate('Membres') . '</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="tous" name="members" value="0" />
                <label class="form-check-label" for="tous">' . adm_translate('Tous') . '</label>
            </div>
        </div>
        <div class="mb-3">
            <label class="col-form-label" for="Mmember[]">' . adm_translate('Groupes') . '</label>';

        echo groupe($member) . '
        </div>';
    } else {
        if ($member == 0) {
            $checked = ' checked="checked"';
        } else {
            $checked = '';
        }

        echo '<input class="form-check-input" type="radio" id="mem" name="members" value="1" />
                <label class="form-check-label" for="mem">' . adm_translate('Membres') . '</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="tous" name="members" value="0"' . $checked . ' />
                <label class="form-check-label" for="tous">' . adm_translate('Tous') . '</label>
            </div>
        </div>
        <div class="mb-3">
            <label class="col-form-label" for="Mmember[]">' . adm_translate('Groupes') . '</label>';

        echo groupe($member) . '
            </div>
        </fieldset>';
    }
}

function sousrub_select($secid)
{
    global $radminsuper, $aid;

    $ok_pub = false;

    $tmp = '<select name="secid" class="form-select">';

    $result = sql_query("SELECT distinct rubid, rubname, ordre 
                         FROM " . sql_prefix('rubriques') . " 
                         ORDER BY ordre");

    while (list($rubid, $rubname) = sql_fetch_row($result)) {

        $rubname = Language::affLangue($rubname);
        $tmp .= '<optgroup label="' . Language::affLangue($rubname) . '">';

        if ($radminsuper == 1) {
            $result2 = sql_query("SELECT secid, secname, ordre 
                                  FROM " . sql_prefix('sections') . " 
                                  WHERE rubid='$rubid' 
                                  ORDER BY ordre");
        } else {
            $result2 = sql_query("SELECT distinct sections.secid, sections.secname, sections.ordre 
                                  FROM " . sql_prefix('sections') . ", " . sql_prefix('publisujet') . " 
                                  WHERE sections.rubid='$rubid' 
                                  AND sections.secid=publisujet.secid2 
                                  AND publisujet.aid='$aid' 
                                  AND publisujet.type='1' 
                                  ORDER BY ordre");
        }

        while (list($secid2, $secname) = sql_fetch_row($result2)) {
            $secname = Language::affLangue($secname);
            $secname = substr($secname, 0, 50);

            $tmp .= '<option value="' . $secid2 . '"';

            if ($secid2 == $secid) {
                $tmp .= ' selected="selected"';
            }

            $tmp .= '>' . $secname . '</option>';
            $ok_pub = true;
        }

        sql_free_result($result2);

        $tmp .= '</optgroup>';
    }

    $tmp .= '</select>';

    sql_free_result($result);

    if (!$ok_pub) {
        $tmp = '';
    }

    return $tmp;
}

function droits_publication($secid)
{
    global $radminsuper, $aid;

    $droits = 0; // 3=mod - 4=delete

    if ($radminsuper != 1) {
        $result = sql_query("SELECT type 
                             FROM " . sql_prefix('publisujet') . " 
                             WHERE secid2='$secid' 
                             AND aid='$aid' 
                             AND type in(3, 4) 
                             ORDER BY type");

        if (sql_num_rows($result) > 0) {
            while (list($type) = sql_fetch_row($result)) {
                $droits = $droits + $type;
            }
        }
    } else {
        $droits = 7;
    }

    return $droits;
}

function sections()
{
    global $hlpfile, $aid, $radminsuper, $f_meta_nom, $f_titre, $adminimg;

    include 'header.php';

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    $result = $radminsuper == 1
        ? sql_query("SELECT rubid, rubname, enligne, ordre 
                     FROM " . sql_prefix('rubriques') . " 
                     ORDER BY ordre")

        : sql_query("SELECT DISTINCT r.rubid, r.rubname, r.enligne, r.ordre 
                     FROM " . sql_prefix('rubriques') . " r, " . sql_prefix('sections') . " s, " . sql_prefix('publisujet') . " p 
                     WHERE (r.rubid=s.rubid AND s.secid=p.secid2 AND p.aid='$aid') 
                     ORDER BY ordre");

    $nb_rub = sql_num_rows($result);

    echo '<hr />
    <ul class="list-group">';

    if ($nb_rub > 0) {
        echo '<li class="list-group-item list-group-item-action"><a href="admin.php?op=sections#ajouter publication"><i class="fa fa-plus-square fa-lg me-2"></i>' . adm_translate('Ajouter une publication') . '</a></li>';
    }

    echo '<li class="list-group-item list-group-item-action"><a href="admin.php?op=new_rub_section&amp;type=rub"><i class="fa fa-plus-square fa-lg me-2"></i>' . adm_translate('Ajouter une nouvelle Rubrique') . '</a></li>';

    if ($nb_rub > 0) {
        echo '<li class="list-group-item list-group-item-action"><a href="admin.php?op=new_rub_section&amp;type=sec" ><i class="fa fa-plus-square fa-lg me-2"></i>' . adm_translate('Ajouter une nouvelle Sous-Rubrique') . '</a></li>';
    }

    if ($radminsuper == 1) {
        echo '<li class="list-group-item list-group-item-action"><a href="admin.php?op=ordremodule"><i class="fa fa-sort-amount-up fa-lg me-2"></i>' . adm_translate('Changer l\'ordre des rubriques') . '</a></li>
        <li class="list-group-item list-group-item-action"><a href="#droits des auteurs"><i class="fa fa-user-edit fa-lg me-2"></i>' . adm_translate('Droits des auteurs') . '</a></li>';
    }

    echo '<li class="list-group-item list-group-item-action"><a href="#publications en attente"><i class="fa fa-clock fa-lg me-2"></i>' . adm_translate('Publication(s) en attente de validation') . '</a></li>
    </ul>';

    if ($nb_rub > 0) {
        $i = -1;

        echo '<hr />
        <h3 class="my-3">' . adm_translate('Liste des rubriques') . '</h3>';

        while (list($rubid, $rubname, $enligne, $ordre) = sql_fetch_row($result)) {
            $i++;

            if ($radminsuper == 1) {
                $href1 = '<a href="admin.php?op=rubriquedit&amp;rubid=' . $rubid . '" title="' . adm_translate('Editer la rubrique') . '" data-bs-toggle="tooltip" data-bs-placement="left"><i class="fa fa-edit fa-lg me-2"></i>&nbsp;';
                $href2 = '</a>';
                $href3 = '<a href="admin.php?op=rubriquedelete&amp;rubid=' . $rubid . '" class="text-danger" title="' . adm_translate('Supprimer la rubrique') . '" data-bs-toggle="tooltip" data-bs-placement="left"><i class="fas fa-trash fa-lg"></i></a>';
            } else {
                $href1 = '';
                $href2 = '';
                $href3 = '';
            }

            $rubname = Language::affLangue($rubname);

            if ($rubname == '') {
                $rubname = adm_translate('Sans nom');
            }

            if ($enligne == 0) {
                $online = '<span class="badge bg-danger ms-1 p-2">' . adm_translate('Hors Ligne') . '</span>';
            } else if ($enligne == 1) {
                $online = '<span class="badge bg-success ms-1 p-2">' . adm_translate('En Ligne') . '</span>';
            }
            echo '<div class="list-group-item bg-light py-2 lead">
                <a href="" class="arrow-toggle text-primary" data-bs-toggle="collapse" data-bs-target="#srub' . $i . '" ><i class="toggle-icon fa fa-caret-down fa-lg"></i></a>&nbsp;' . $rubname . ' ' . $online . ' <span class="float-end">' . $href1 . $href2 . $href3 . '</span>
            </div>';

            if ($radminsuper == 1) {
                $result2 = sql_query("SELECT DISTINCT secid, secname, ordre 
                                      FROM " . sql_prefix('sections') . " 
                                      WHERE rubid='$rubid' 
                                      ORDER BY ordre");
            } else {
                $result2 = sql_query("SELECT DISTINCT sections.secid, sections.secname, sections.ordre 
                                      FROM " . sql_prefix('sections') . ", " . sql_prefix('publisujet') . " 
                                      WHERE sections.rubid='$rubid' 
                                      AND sections.secid=publisujet.secid2 
                                      AND publisujet.aid='$aid' 
                                      ORDER BY ordre");
            }

            if (sql_num_rows($result2) > 0) {
                echo '<div id="srub' . $i . '" class=" mb-3 collapse ">
                <div class="list-group-item d-flex py-2"><span class="badge bg-secondary me-2 p-2">' . sql_num_rows($result2) . '</span><strong class="">' . adm_translate('Sous-rubriques') . '</strong>';

                if ($radminsuper == 1) {
                    echo '<span class="ms-auto"><a href="admin.php?op=ordrechapitre&amp;rubid=' . $rubid . '&amp;rubname=' . $rubname . '" title="' . adm_translate('Changer l\'ordre des sous-rubriques') . '" data-bs-toggle="tooltip" data-bs-placement="left" ><i class="fa fa-sort-amount-up fa-lg"></i></a></span>';
                }

                echo '</div>';

                while (list($secid, $secname) = sql_fetch_row($result2)) {
                    $droit_pub = droits_publication($secid);
                    $secname = Language::affLangue($secname);

                    $result3 = sql_query("SELECT artid, title 
                                          FROM " . sql_prefix('seccont') . " 
                                          WHERE secid='$secid' 
                                          ORDER BY ordre");

                    echo '<div class="list-group-item d-flex py-2">';

                    echo (sql_num_rows($result3) > 0) ?
                        '<a href="" class="arrow-toggle text-primary " data-bs-toggle="collapse" data-bs-target="#lst_sect_' . $secid . '" ><i class="toggle-icon fa fa-caret-down fa-lg"></i></a>' :
                        '<span class=""> - </span>';

                    echo '&nbsp;
                    ' . $secname . '
                    <span class="ms-auto"><a href="sections.php?op=listarticles&amp;secid=' . $secid . '&amp;prev=1" ><i class="fa fa-eye fa-lg me-2 py-2"></i></a>';

                    if ($droit_pub > 0 and $droit_pub != 4) { // à revoir pas suffisant
                        echo '<a href="admin.php?op=sectionedit&amp;secid=' . $secid . '" title="' . adm_translate('Editer la sous-rubrique') . '" data-bs-toggle="tooltip" data-bs-placement="left"><i class="fa fa-edit fa-lg py-2 me-2"></i></a>';
                    }

                    if (($droit_pub == 7) or ($droit_pub == 4)) {
                        echo '<a href="admin.php?op=sectiondelete&amp;secid=' . $secid . '" title="' . adm_translate('Supprimer la sous-rubrique') . '" data-bs-toggle="tooltip" data-bs-placement="left"><i class="fas fa-trash fa-lg text-danger py-2"></i></a>';
                    }

                    echo '</span>
                    </div>';

                    if (sql_num_rows($result3) > 0) {
                        //$ibid = true; ??

                        echo '<div id="lst_sect_' . $secid . '" class=" collapse">
                        <li class="list-group-item d-flex">
                        <span class="badge bg-secondary ms-4 p-2">' . sql_num_rows($result3) . '</span>&nbsp;<strong class=" text-capitalize">' . adm_translate('publications') . '</strong>';

                        if ($radminsuper == 1) {
                            echo '<span class="ms-auto"><a href="admin.php?op=ordrecours&secid=' . $secid . '&amp;secname=' . $secname . '" title="' . adm_translate('Changer l\'ordre des publications') . '" data-bs-toggle="tooltip" data-bs-placement="left">&nbsp;<i class="fa fa-sort-amount-up fa-lg"></i></a></span>';
                        }

                        echo '</li>';

                        while (list($artid, $title) = sql_fetch_row($result3)) {
                            if ($title == '') {
                                $title = adm_translate('Sans titre');
                            }

                            echo '<li class="list-group-item list-group-item-action d-flex"><span class="ms-4">' . Language::affLangue($title) . '</span>
                            <span class="ms-auto">
                            <a href="sections.php?op=viewarticle&amp;artid=' . $artid . '&amp;prev=1"><i class="fa fa-eye fa-lg"></i></a>&nbsp;';

                            if ($droit_pub > 0 and $droit_pub != 4) {
                                echo '<a href="admin.php?op=secartedit&amp;artid=' . $artid . '" ><i class="fa fa-edit fa-lg"></i></a>&nbsp;';
                            }

                            if (($droit_pub == 7) or ($droit_pub == 4)) {
                                echo '<a href="admin.php?op=secartdelete&amp;artid=' . $artid . '" class="text-danger" title="' . adm_translate('Supprimer') . '" data-bs-toggle="tooltip"><i class="far fa-trash fa-lg"></i></a>';
                            }

                            echo '</span>
                            </li>';
                        }

                        echo '</div>';
                    }
                }

                echo '</div>';
            }
        }

        echo '
        <hr />
        <h3 class="my-3">' . adm_translate('Editer une publication') . '</h3>
        <form action="admin.php" method="post">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="artid">ID</label>
                <div class="col-sm-8">
                <input type="number" class="form-control" id="artid" name="artid" min="0" max="999999999" />
                </div>
            </div>
            <input type="hidden" name="op" value="secartedit" />
        </form>';

        // Ajout d'une publication
        $autorise_pub = sousrub_select('');

        if ($autorise_pub) {
            echo '<hr />
            <h3 class="mb-3"><a name="ajouter publication">' . adm_translate('Ajouter une publication') . '</a></h3>
            <form action="admin.php" method="post" name="adminForm">
                <div class="mb-3 row">
                <label class="col-form-label col-12" for="secid">' . adm_translate('Sous-rubrique') . '</label>
                <div class="col-12">
                ' . $autorise_pub . '
                </div>
                </div>
                <div class="mb-3 row">
                <label class="col-form-label col-12" for="title">' . adm_translate('Titre') . '</label>
                <div class=" col-12">
                    <textarea class="form-control" name="title" rows="2"></textarea>
                </div>
                </div>
                <div class="mb-3 row">
                <label class="col-form-label col-12" for="content">' . adm_translate('Contenu') . '</label>
                <div class=" col-12">
                    <textarea class="tin form-control" name="content" rows="30"></textarea>
                </div>
                </div>
                ' . Editeur::affEditeur('content', '') . '
                <input type="hidden" name="op" value="secarticleadd" />
                <input type="hidden" name="autho" value="' . $aid . '" />';

            droits("0");

            echo '<div class="mb-3">
                <input class="btn btn-primary" type="submit" value="' . adm_translate('Ajouter') . '" />
                </div>
            </form>';

            // ca c'est pas bon incomplet
            if ($radminsuper != 1) {
                echo '<p class="blockquote">' . adm_translate('Une fois que vous aurez validé cette publication, elle sera intégrée en base temporaire, et l\'administrateur sera prévenu. Il visera cette publication et la mettra en ligne dans les meilleurs délais. Il est normal que pour l\'instant, cette publication n\'apparaisse pas dans l\'arborescence.') . '</p>';
            }
        }
    }

    $enattente = '';

    if ($radminsuper == 1) {
        $result = sql_query("SELECT distinct artid, secid, title, content, author 
                             FROM " . sql_prefix('seccont_tempo') . " 
                             ORDER BY artid");

        $nb_enattente = sql_num_rows($result);

        while (list($artid, $secid, $title, $content, $author) = sql_fetch_row($result)) {
            $enattente .= '<li class="list-group-item list-group-item-action" ><div class="d-flex flex-row align-items-center"><span class="flex-grow-1 pe-4">' . Language::affLangue($title) . '<br /><span class="text-body-secondary"><i class="fa fa-user fa-lg me-1"></i>[' . $author . ']</span></span><span class="text-center"><a href="admin.php?op=secartupdate&amp;artid=' . $artid . '">' . adm_translate('Editer') . '<br /><i class="fa fa-edit fa-lg"></i></a></span></div>';
        }
    } else {
        $result = sql_query("SELECT distinct seccont_tempo.artid, seccont_tempo.title, seccont_tempo.author 
                             FROM " . sql_prefix('seccont_tempo') . ", " . sql_prefix('') . "publisujet 
                             WHERE seccont_tempo.secid=publisujet.secid2 
                             AND publisujet.aid='$aid' 
                             AND (publisujet.type='1' OR publisujet.type='2')");

        $nb_enattente = sql_num_rows($result);

        while (list($artid, $title, $author) = sql_fetch_row($result)) {
            $enattente .= '<li class="list-group-item list-group-item-action" ><div class="d-flex flex-row align-items-center"><span class="flex-grow-1 pe-4">' . Language::affLangue($title) . '<br /><span class="text-body-secondary"><i class="fa fa-user fa-lg me-1"></i>[' . $author . ']</span></span><span class="text-center"><a href="admin.php?op=secartupdate&amp;artid=' . $artid . '">' . adm_translate('Editer') . '<br /><i class="fa fa-edit fa-lg"></i></a></span></div>';
        }
    }

    echo '<hr />
    <h3 class="mb-3"><a name="publications en attente"><i class="far fa-clock fa-lg me-1"></i>' . adm_translate('Publication(s) en attente de validation') . '</a><span class="badge bg-danger float-end">' . $nb_enattente . '</span></h3>
    <ul class="list-group">
    ' . $enattente . '
    </ul>';

    if ($radminsuper == 1) {
        echo  '<hr />
        <h3 class="mb-3"><a name="droits des auteurs"><i class="fa fa-user-edit me-2"></i>' . adm_translate('Droits des auteurs') . '</a></h3>';

        $result = sql_query("SELECT aid, name, radminsuper 
                             FROM " . sql_prefix('authors'));

        echo '<div class="row">';

        while (list($Xaid, $name, $Xradminsuper) = sql_fetch_row($result)) {
            if (!$Xradminsuper) {
                echo '<div class="col-sm-4">
                <div class="card my-2 p-1">
                    <div class="card-body p-1">
                        <i class="fa fa-user fa-lg me-1"></i><br />' . $Xaid . '&nbsp;/&nbsp;' . $name . '<br />
                        <a href="admin.php?op=droitauteurs&amp;author=' . $Xaid . '">' . adm_translate('Modifier l\'information') . '</a>
                    </div>
                </div>
                </div>';
            }
        }

        echo '</div>';
    }

    Validation::adminFoot('', '', '', '');
}

function new_rub_section($type)
{
    global $hlpfile, $aid, $radminsuper, $f_meta_nom, $f_titre, $adminimg;
    include 'header.php';

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    $arg1 = '';

    if ($type == 'sec') {
        echo '<hr />
        <h3 class="mb-3">' . adm_translate('Ajouter une nouvelle Sous-Rubrique') . '</h3>
        <form action="admin.php" method="post" id="newsection" name="adminForm">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="rubref">' . adm_translate('Rubriques') . '</label>
                <div class="col-sm-8">
                <select class="form-select" id="rubref" name="rubref">';

        if ($radminsuper == 1) {
            $result = sql_query("SELECT rubid, rubname 
                                 FROM " . sql_prefix('rubriques') . " 
                                 ORDER BY ordre");
        } else {
            $result = sql_query("SELECT DISTINCT r.rubid, r.rubname 
                                 FROM " . sql_prefix('rubriques') . " r 
                                 LEFT JOIN " . sql_prefix('') . "sections s on r.rubid= s.rubid 
                                 LEFT JOIN " . sql_prefix('') . "publisujet p on s.secid= p.secid2 
                                 WHERE p.aid='$aid'");
        }

        while (list($rubid, $rubname) = sql_fetch_row($result)) {
            echo '<option value="' . $rubid . '">' . Language::affLangue($rubname) . '</option>';
        }

        echo '</select>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 col-md-4" for="image">' . adm_translate('Image pour la Sous-Rubrique') . '</label>
                <div class="col-sm-8">
                <input type="text" class="form-control" name="image" />
                </div>
            </div>
            <div class="mb-3">
                <label class="col-form-label" for="secname">' . adm_translate('Titre') . '</label>
                <textarea  class="form-control" id="secname" name="secname" maxlength="255" rows="2" required="required"></textarea>
                <span class="help-block text-end"><span id="countcar_secname"></span></span>
            </div>
            <div class="mb-3">
                <label class="col-form-label" for="introd">' . adm_translate('Texte d\'introduction') . '</label>
                <textarea class="tin form-control" name="introd" rows="30"></textarea>';

        echo Editeur::affEditeur("introd", '');

        echo '</div>';

        droits(0);

        echo '<div class="mb-3">
            <input type="hidden" name="op" value="sectionmake" />
            <button class="btn btn-primary col-sm-6 col-12 col-md-4" type="submit" /><i class="fa fa-plus-square fa-lg"></i>&nbsp;' . adm_translate('Ajouter') . '</button>
            <button class="btn btn-secondary col-sm-6 col-12 col-md-4" type="button" onclick="javascript:history.back()">' . adm_translate('Retour en arrière') . '</button>
        </div>
        </form>';

        $arg1 = 'var formulid = ["newsection"];
            inpandfieldlen("secname",255);';
    } else if ($type == "rub") {
        echo '<hr />
            <h3 class="mb-3">' . adm_translate('Ajouter une nouvelle Rubrique') . '</h3>
            <form action="admin.php" method="post" id="newrub" name="adminForm">
                <div class="mb-3">
                <label class="col-form-label" for="rubname">' . adm_translate('Nom de la Rubrique') . '</label>
                <textarea class="form-control" id="rubname" name="rubname" rows="2" maxlength="255" required="required"></textarea>
                <span class="help-block text-end" id="countcar_rubname"></span>
                </div>
                <div class="mb-3">
                <label class="col-form-label" for="introc">' . adm_translate('Texte d\'introduction') . '</label>
                <textarea class="tin form-control" id="introc" name="introc" rows="30" ></textarea>
                </div>';

        echo Editeur::affEditeur('introc', '');

        echo '<div class="mb-3">
                <input type="hidden" name="op" value="rubriquemake" />
                <button class="btn btn-primary" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;' . adm_translate('Ajouter') . '</button>
                <button class="btn btn-secondary" type="button" onclick="javascript:history.back()">' . adm_translate('Retour en arrière') . '</button>
                </div>
            </form>';

        $arg1 = 'var formulid = ["newrub"];
            inpandfieldlen("rubname",255);';
    }

    Validation::adminFoot('fv', '', $arg1, '');
}

// Fonction publications connexes
function publishcompat($article)
{
    global $hlpfile, $aid, $radminsuper, $f_meta_nom, $f_titre, $adminimg;

    $result2 = sql_query("SELECT title 
                          FROM " . sql_prefix('seccont') . " 
                          WHERE artid='$article'");

    list($titre) = sql_fetch_row($result2);

    include 'header.php';

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    $result = sql_query("SELECT rubid, rubname, enligne, ordre 
                         FROM " . sql_prefix('rubriques') . " 
                         ORDER BY ordre");

    echo '<hr />
    <h3 class="mb-3">' . adm_translate('Publications connexes') . ' : <span class="text-body-secondary">' . Language::affLangue($titre) . '</span></h3>
    <form action="admin.php" method="post">';

    $i = 0;

    while (list($rubid, $rubname, $enligne, $ordre) = sql_fetch_row($result)) {
        if ($enligne == 0) {
            $online = adm_translate('Hors Ligne');
            $cla = "danger";
        } else if ($enligne == 1) {
            $online = adm_translate('En Ligne');
            $cla = "success";
        }

        echo '<div class="list-group-item bg-light">
            <a class="arrow-toggle text-primary" data-bs-toggle="collapse" data-bs-target="#lst_' . $rubid . '" ><i class="toggle-icon fa fa-caret-down fa-lg"></i></a>&nbsp;' . Language::affLangue($rubname) . '<span class="badge bg-' . $cla . ' float-end">' . $online . '</span>
        </div>';

        if ($radminsuper == 1) {
            $result2 = sql_query("SELECT secid, secname 
                                  FROM " . sql_prefix('sections') . " 
                                  WHERE rubid='$rubid' 
                                  ORDER BY ordre");
        } else {
            $result2 = sql_query("SELECT DISTINCT sections.secid, sections.secname, sections.ordre 
                                  FROM " . sql_prefix('sections') . ", " . sql_prefix('publisujet') . " 
                                  WHERE sections.rubid='$rubid' 
                                  AND sections.secid=publisujet.secid2 
                                  AND publisujet.aid='$aid' 
                                  AND publisujet.type='1' 
                                  ORDER BY ordre");
        }

        if (sql_num_rows($result2) > 0) {
            echo '<ul id="lst_' . $rubid . '" class="list-group mb-1 collapse">';

            while (list($secid, $secname) = sql_fetch_row($result2)) {
                echo '<li class="list-group-item"><strong class="ms-3" title="' . adm_translate('sous-rubrique') . '" data-bs-toggle="tooltip">' . Language::affLangue($secname) . '</strong></li>';

                $result3 = sql_query("SELECT artid, title 
                                      FROM " . sql_prefix('seccont') . " 
                                      WHERE secid='$secid' 
                                      ORDER BY ordre");

                if (sql_num_rows($result3) > 0) {

                    while (list($artid, $title) = sql_fetch_row($result3)) {
                        $i++;

                        $result4 = sql_query("SELECT id2 
                                              FROM " . sql_prefix('compatsujet') . " 
                                              WHERE id2='$artid' 
                                              AND id1='$article'");

                        echo '<li class="list-group-item list-group-item-action"><div class="form-check ms-3">';

                        if (sql_num_rows($result4) > 0) {
                            echo '<input class="form-check-input" type="checkbox"  id="admin_rub' . $i . '" name="admin_rub[' . $i . ']" value="' . $artid . '" checked="checked" />';
                        } else {
                            echo '<input class="form-check-input" type="checkbox" id="admin_rub' . $i . '" name="admin_rub[' . $i . ']" value="' . $artid . '" />';
                        }

                        echo '<label class="form-check-label" for="admin_rub' . $i . '">' . Language::affLangue($title) . '</label></div></li>';
                    }
                }
            }

            echo '</ul>';
        }
    }

    echo '
        <input type="hidden" name="article" value="' . $article . '" />
        <input type="hidden" name="op" value="updatecompat" />
        <input type="hidden" name="idx" value="' . $i . '" />
        <div class="mb-3 mt-3">
            <button class="btn btn-primary" type="submit">' . adm_translate('Valider') . '</button>&nbsp;<input class="btn btn-secondary" type="button" onclick="javascript:history.back()" value="' . adm_translate('Retour en arrière') . '" />
        </div>
    </form>';

    Validation::adminFoot('', '', '', '');
}

function updatecompat($article, $admin_rub, $idx)
{
    $result = sql_query("DELETE FROM " . sql_prefix('compatsujet') . " 
                         WHERE id1='$article'");

    for ($j = 1; $j < ($idx + 1); $j++) {
        if ($admin_rub[$j] != '') {
            $result = sql_query("INSERT INTO " . sql_prefix('compatsujet') . " 
                                 VALUES ('$article','$admin_rub[$j]')");
        }
    }

    global $aid;
    Log::ecrireLog('security', sprintf('UpdateCompatSujets(%s) by AID : %s', $article, $aid), '');

    Header('Location: admin.php?op=secartedit&artid=' . $article);
}
// Fonction publications connexes

// Fonctions RUBRIQUES
function rubriquedit($rubid)
{
    global $hlpfile, $radminsuper, $f_meta_nom, $f_titre, $adminimg;

    if ($radminsuper != 1) {
        Header('Location: admin.php?op=sections');
    }

    $result = sql_query("SELECT rubid, rubname, intro, enligne, ordre 
                         FROM " . sql_prefix('rubriques') . " 
                         WHERE rubid='$rubid'");

    list($rubid, $rubname, $intro, $enligne, $ordre) = sql_fetch_row($result);

    if (!sql_num_rows($result)) {
        Header('Location: admin.php?op=sections');
    }

    include 'header.php';

    GraphicAdmin($hlpfile);

    $result2 = sql_query("SELECT secid 
                          FROM " . sql_prefix('sections') . " 
                          WHERE rubid='$rubid'");

    $number = sql_num_rows($result2);

    $rubname = stripslashes($rubname);
    $intro = stripslashes($intro);

    adminhead($f_meta_nom, $f_titre, $adminimg);

    echo '<hr />
    <h3 class="mb-3">' . adm_translate('Editer une Rubrique : ') . ' <span class="text-body-secondary">' . Language::affLangue($rubname) . ' #' . $rubid . '</span></h3>';

    if ($number) {
        echo '<span class="badge bg-secondary">' . $number . '</span>&nbsp;' . adm_translate('sous-rubrique(s) attachée(s)');
    }

    echo '<form id="rubriquedit" action="admin.php" method="post" name="adminForm">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="rubname">' . adm_translate('Titre') . '</label>
                <div class="col-sm-12">
                <textarea class="form-control" id="rubname" name="rubname" maxlength ="255" rows="2" required="required">' . $rubname . '</textarea>
                <span class="help-block text-end"><span id="countcar_rubname"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="introc">' . adm_translate('Texte d\'introduction') . '</label>
                <div class="col-sm-12">
                <textarea class="tin form-control" id="introc" name="introc" rows="30" >' . $intro . '</textarea>
                </div>
            </div>
            ' . Editeur::affEditeur('introc', '') . '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-3 pt-0" for="enligne">' . adm_translate('En Ligne') . '</label>';

    if ($radminsuper == 1) {
        if ($enligne == 1) {
            $sel1 = 'checked="checked"';
            $sel2 = '';
        } else {
            $sel1 = '';
            $sel2 = 'checked="checked"';
        }
    }

    echo '<div class="col-sm-9">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="enligne_n" name="enligne" value="0" ' . $sel2 . ' />
                    <label class="form-check-label" for="enligne_n">' . adm_translate('Non') . '</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="enligne_y" name="enligne" value="1" ' . $sel1 . ' />
                    <label class="form-check-label" for="enligne_y">' . adm_translate('Oui') . '</label>
                </div>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                <input type="hidden" name="rubid" value="' . $rubid . '" />
                <input type="hidden" name="op" value="rubriquechange" />
                <button class="btn btn-primary" type="submit">' . adm_translate('Enregistrer') . '</button>&nbsp;
                <input class="btn btn-secondary" type="button" value="' . adm_translate('Retour en arrière') . '" onclick="javascript:history.back()" />
                </div>
            </div>
        </form>';

    $arg1 = 'var formulid = ["rubriquedit"];
        inpandfieldlen("rubname",255);';

    Validation::adminFoot('fv', '', $arg1, '');
}

function rubriquemake($rubname, $introc)
{
    global $radminsuper, $aid;

    $rubname = stripslashes(Sanitize::fixQuotes($rubname));
    $introc = stripslashes(Sanitize::fixQuotes(data_image_to_file_url($introc, 'modules/upload/storage/rub')));

    sql_query("INSERT INTO " . sql_prefix('rubriques') . " 
               VALUES (NULL, '$rubname', '$introc', '0', '0')");

    //mieux ? création automatique d'une sous rubrique avec droits ... ?
    if ($radminsuper != 1) {
        $result = sql_query("SELECT rubid 
                             FROM " . sql_prefix('rubriques') . " 
                             ORDER BY rubid DESC LIMIT 1");

        list($rublast) = sql_fetch_row($result);

        sql_query("INSERT INTO " . sql_prefix('sections') . " 
                   VALUES (NULL,'A modifier !', '', '', '$rublast', '<p>Cette sous-rubrique a été créé automatiquement. <br />Vous pouvez la personaliser et ensuite rattacher les publications que vous souhaitez.</p>','99','0')");

        $result = sql_query("SELECT secid 
                             FROM " . sql_prefix('sections') . " 
                             ORDER BY secid DESC 
                             LIMIT 1");

        list($seclast) = sql_fetch_row($result);

        droitsalacreation($aid, $seclast);

        Log::ecrireLog('security', sprintf('CreateSections(Vide) by AID : %s (via system)', $aid), '');
    }

    Log::ecrireLog('security', sprintf('CreateRubriques(%s) by AID : %s', $rubname, $aid), '');

    Header('Location: admin.php?op=ordremodule');
}

function rubriquechange($rubid, $rubname, $introc, $enligne)
{
    $rubname = stripslashes(Sanitize::fixQuotes($rubname));
    $introc = data_image_to_file_url($introc, 'modules/upload/storage/rub');
    $introc = stripslashes(Sanitize::fixQuotes($introc));

    sql_query("UPDATE " . sql_prefix('rubriques') . " 
               SET rubname='$rubname', intro='$introc', enligne='$enligne' 
               WHERE rubid='$rubid'");

    global $aid;
    Log::ecrireLog('security', sprintf('UpdateRubriques(%s, %s) by AID : %s', $rubid, $rubname, $aid), '');

    Header('Location: admin.php?op=sections');
}
// Fonctions RUBRIQUES

// Fonctions SECTIONS
function sectionedit($secid)
{
    global $hlpfile, $radminsuper, $f_meta_nom, $f_titre, $adminimg, $aid;

    include 'header.php';

    GraphicAdmin($hlpfile);

    $result = sql_query("SELECT secid, secname, image, userlevel, rubid, intro 
                         FROM " . sql_prefix('sections') . " 
                         WHERE secid='$secid'");

    list($secid, $secname, $image, $userlevel, $rubref, $intro) = sql_fetch_row($result);

    $secname = stripslashes($secname);
    $intro = stripslashes($intro);

    adminhead($f_meta_nom, $f_titre, $adminimg);

    echo '<hr />
    <h3 class="mb-3">' . adm_translate('Sous-rubrique') . ' : <span class="text-body-secondary">' . Language::affLangue($secname) . '</span></h3>';

    $result2 = sql_query("SELECT artid 
                          FROM " . sql_prefix('seccont') . " 
                          WHERE secid='$secid'");

    $number = sql_num_rows($result2);

    if ($number) {
        echo '<span class="badge bg-secondary p-2 me-2">' . $number . ' </span>' . adm_translate('publication(s) attachée(s)');
    }

    echo '<form id="sectionsedit" action="admin.php" method="post" name="adminForm">
            <div class="mb-3">
                <label class="col-form-label" for="rubref">' . adm_translate('Rubriques') . '</label>';


    if ($radminsuper == 1) {
        $result = sql_query("SELECT rubid, rubname 
                             FROM " . sql_prefix('rubriques') . " 
                             ORDER BY ordre");
    } else {
        $result = sql_query("SELECT DISTINCT r.rubid, r.rubname 
                             FROM " . sql_prefix('rubriques') . " r 
                             LEFT JOIN " . sql_prefix('sections') . " s on r.rubid= s.rubid 
                             LEFT JOIN " . sql_prefix('publisujet') . " p on s.secid= p.secid2 
                             WHERE p.aid='$aid'");
    }

    echo '<select class="form-select" id="rubref" name="rubref">';

    while (list($rubid, $rubname) = sql_fetch_row($result)) {
        $sel = $rubref == $rubid ? 'selected="selected"' : '';

        echo '<option value="' . $rubid . '" ' . $sel . '>' . Language::affLangue($rubname) . '</option>';
    }

    echo '</select>
        </div>';

    // ici on a(vait) soit le select qui permet de changer la sous rubrique de rubrique (ca c'est good) 
    // soit un input caché avec la valeur fixé de la rubrique...donc ICI un author ne peut pas changer sa 
    //sous rubrique de rubrique ...il devrait pouvoir le faire dans une sous-rubrique ou il a des "droits" ??
    /*
    if ($radminsuper == 1) {
        echo '<select class="form-select" id="rubref" name="rubref">';

        $result = sql_query("SELECT rubid, rubname 
                             FROM ".sql_prefix('rubriques')." 
                             ORDER BY ordre");

        while(list($rubid, $rubname) = sql_fetch_row($result)) {
            $sel = $rubref == $rubid ? 'selected="selected"' : '';
            
            echo '<option value="'. $rubid .'" '. $sel .'>'. Language::affLangue($rubname) .'</option>';
        }

        echo '</select>
        </div>';

    } else {
        echo '<input type="hidden" name="rubref" value="'.$rubref.'" />';

        $result = sql_query("SELECT rubname 
                             FROM ".sql_prefix('rubriques')." 
                             WHERE rubid='$rubref'");

        list($rubname) = sql_fetch_row($result);

        echo '<pan class="ms-2">'. Language::affLangue($rubname) .'</span>';
    }
    */

    //ici
    echo '<div class="mb-3">
        <label class="col-form-label" for="secname">' . adm_translate('Sous-rubrique') . '</label>
        <textarea class="form-control" id="secname" name="secname" rows="4" maxlength="255" required="required">' . $secname . '</textarea>
        <span class="help-block text-end"><span id="countcar_secname"></span></span>
    </div>
    <div class="mb-3">
        <label class="col-form-label" for="image">' . adm_translate('Image') . '</label>
        <input type="text" class="form-control" id="image" name="image" maxlength="255" value="' . $image . '" />
        <span class="help-block text-end"><span id="countcar_image"></span></span>
    </div>
    <div class="mb-3">
        <label class="col-form-label" for="introd">' . adm_translate('Texte d\'introduction') . '</label>
        <textarea class="tin form-control" id="introd" name="introd" rows="20">' . $intro . '</textarea>
    </div>';

    echo Editeur::affEditeur('introd', '');

    droits($userlevel);

    $droit_pub = droits_publication($secid);

    if ($droit_pub == 3 or $droit_pub == 7) {
        echo '<input type="hidden" name="secid" value="' . $secid . '" />
            <input type="hidden" name="op" value="sectionchange" />
            <button class="btn btn-primary" type="submit">' . adm_translate('Enregistrer') . '</button>';
    }

    echo '<input class="btn btn-secondary" type="button" value="' . adm_translate('Retour en arrière') . '" onclick="javascript:history.back()" />
    </form>';

    $arg1 = 'var formulid = ["sectionsedit"];
        inpandfieldlen("secname",255);
        inpandfieldlen("image",255);';

    Validation::adminFoot('fv', '', $arg1, '');
}

function sectionmake($secname, $image, $members, $Mmembers, $rubref, $introd)
{
    global $radminsuper, $aid;

    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);

        if ($members == 0) {
            $members = 1;
        }
    }

    $secname = stripslashes(Sanitize::fixQuotes($secname));
    $rubref = stripslashes(Sanitize::fixQuotes($rubref));
    $image = stripslashes(Sanitize::fixQuotes($image));

    $introd = stripslashes(Sanitize::fixQuotes(data_image_to_file_url($introd, 'modules/upload/storage/sec')));

    sql_query("INSERT INTO " . sql_prefix('sections') . " 
               VALUES (NULL,'$secname', '$image', '$members', '$rubref', '$introd', '99', '0')");

    if ($radminsuper != 1) {
        $result = sql_query("SELECT secid 
                             FROM " . sql_prefix('sections') . " 
                             ORDER BY secid DESC
                             LIMIT 1");

        list($secid) = sql_fetch_row($result);

        droitsalacreation($aid, $secid);
    }

    Log::ecrireLog('security', sprintf('CreateSections(%s) by AID : %s', $secname, $aid), '');

    Header('Location: admin.php?op=sections');
}

function sectionchange($secid, $secname, $image, $members, $Mmembers, $rubref, $introd)
{
    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);

        if ($members == 0) {
            $members = 1;
        }
    }

    $secname = stripslashes(Sanitize::fixQuotes($secname));
    $image = stripslashes(Sanitize::fixQuotes($image));

    $introd = stripslashes(Sanitize::fixQuotes(data_image_to_file_url($introd, 'modules/upload/storage/sec')));

    sql_query("UPDATE " . sql_prefix('sections') . " 
               SET secname='$secname', image='$image', userlevel='$members', rubid='$rubref', intro='$introd' 
               WHERE secid='$secid'");

    global $aid;
    Log::ecrireLog('security', sprintf('UpdateSections(%s, %s) by AID : %s', $secid, $secname, $aid), '');

    Header('Location: admin.php?op=sections');
}
// Fonctions SECTIONS

// Fonction ARTICLES
function secartedit($artid)
{
    global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

    $result2 = sql_query("SELECT author, artid, secid, title, content, userlevel 
                          FROM " . sql_prefix('seccont') . " 
                          WHERE artid='$artid'");

    list($author, $artid, $secid, $arttitle, $content, $userlevel) = sql_fetch_row($result2);

    if (!$artid) {
        Header('Location: admin.php?op=sections');
    }

    include 'header.php';

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    $arttitle = stripslashes($arttitle);
    $content = stripslashes(data_image_to_file_url($content, 'cache/s'));

    echo '<hr />
    <h3 class="mb-3">' . adm_translate('Editer une publication') . '</h3>
        <form action="admin.php" method="post" id="secartedit" name="adminForm">
            <input type="hidden" name="artid" value="' . $artid . '" />
            <input type="hidden" name="op" value="secartchange" />
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="secid">' . adm_translate('Sous-rubriques') . '</label>
                <div class="col-sm-8">';

    // la on déraille ???
    $tmp_autorise = sousrub_select($secid);

    if ($tmp_autorise) {
        echo $tmp_autorise;
    } else {
        $result = sql_query("SELECT secname 
                             FROM " . sql_prefix('sections') . " 
                             WHERE secid='$secid'");

        list($secname) = sql_fetch_row($result);

        echo "<b>" . Language::affLangue($secname) . "</b>";
        echo '<input type="hidden" name="secid" value="' . $secid . '" />';
    }

    echo '</div>
            </div>';

    if ($tmp_autorise) {
        echo '<a href="admin.php?op=publishcompat&amp;article=' . $artid . '">' . adm_translate('Publications connexes') . '</a>';
    }

    echo '<div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="title">' . adm_translate('Titre') . '</label>
                <div class="col-sm-12">
                <textarea class="form-control" id="title" name="title" rows="2">' . $arttitle . '</textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="content">' . adm_translate('Contenu') . '</label>
                <div class="col-sm-12">
                <textarea class="tin form-control" id="content" name="content" rows="30" >' . $content . '</textarea>
                </div>
            </div>';

    echo Editeur::affEditeur('content', '');

    echo '<div class="mb-3 row">
        <div class="col-sm-12">';

    droits($userlevel);

    $droits_pub = droits_publication($secid);

    if ($droits_pub == 3 or $droits_pub == 7) {
        echo '<input class="btn btn-primary" type="submit" value="' . adm_translate('Enregistrer') . '" />&nbsp;';
    }

    echo '<input class="btn btn-secondary" type="button" value="' . adm_translate('Retour en arrière') . '" onclick="javascript:history.back()" />
            </div>
        </div>
    </form>';

    Validation::adminFoot('', '', '', '');
}

function secartupdate($artid)
{
    global $hlpfile, $aid, $radminsuper, $f_meta_nom, $f_titre, $adminimg;

    $result = sql_query("SELECT author, artid, secid, title, content, userlevel 
                         FROM " . sql_prefix('seccont_tempo') . " 
                         WHERE artid='$artid'");

    list($author, $artid, $secid, $title, $content, $userlevel) = sql_fetch_row($result);

    $testpubli = sql_query("SELECT type 
                            FROM " . sql_prefix('publisujet') . " 
                            WHERE secid2='$secid' 
                            AND aid='$aid' 
                            AND type='1'");

    list($test_publi) = sql_fetch_row($testpubli);

    if ($test_publi == 1) {
        $debut = '<div class="alert alert-info">' . adm_translate('Vos droits de publications vous permettent de mettre à jour ou de supprimer ce contenu mais pas de la mettre en ligne sur le site.') . '</div>';

        $fin = '<div class="mb-3 row">
            <div class="col-12">
                <select class="form-select" name="op">
                <option value="secartchangeup" selected="selected">' . adm_translate('Mettre à jour') . '</option>
                <option value="secartdelete2">' . adm_translate('Supprimer') . '</option>
                </select>
            </div>
        </div>
        <input type="submit" class="btn btn-primary" name="submit" value="' . adm_translate('Ok') . '" />';
    }

    $testpubli = sql_query("SELECT type 
                            FROM " . sql_prefix('publisujet') . " 
                            WHERE secid2='$secid' 
                            AND aid='$aid' 
                            AND type='2'");

    list($test_publi) = sql_fetch_row($testpubli);

    if (($test_publi == 2) or ($radminsuper == 1)) {
        $debut = '<div class="alert alert-success">' . adm_translate('Vos droits de publications vous permettent de mettre à jour, de supprimer ou de le mettre en ligne sur le site ce contenu.') . '<br /></div>';

        $fin = '<div class="mb-3 row">
            <div class="col-12">
                <select class="form-select" name="op">
                <option value="secartchangeup" selected="selected">' . adm_translate('Mettre à jour') . '</option>
                <option value="secartdelete2">' . adm_translate('Supprimer') . '</option>
                <option value="secartpublish">' . adm_translate('Publier') . '</option>
                </select>
            </div>
        </div>
        <input type="submit" class="btn btn-primary" name="submit" value="' . adm_translate('Ok') . '" />';
    }

    $fin .= '&nbsp;<input class="btn btn-secondary" type="button" value="' . adm_translate('Retour en arrière') . '" onclick="javascript:history.back()" />';

    include 'header.php';

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    echo '<hr />
    <h3 class="mb-3">' . adm_translate('Editer une publication') . '</h3>';

    echo $debut;

    $title = stripslashes($title);
    $content = stripslashes(data_image_to_file_url($content, 'cache/s'));

    echo '<form id="secartupdate" action="admin.php" method="post" name="adminForm">
        <input type="hidden" name="artid" value="' . $artid . '" />
        <div class="mb-3 row">
            <label class="col-form-label col-sm-4" for="secid">' . adm_translate('Sous-rubrique') . '</label>
            <div class="col-sm-8">';

    $tmp_autorise = sousrub_select($secid); /// a affiner pas bon car dans certain cas on peut donc publier dans une sous rubrique sur laquelle on n'a pas les droits

    if ($tmp_autorise) {
        echo $tmp_autorise;
    } else {
        $result = sql_query("SELECT secname 
                             FROM " . sql_prefix('sections') . " 
                             WHERE secid='$secid'");

        list($secname) = sql_fetch_row($result);

        echo '<strong>' . Language::affLangue($secname) . '</strong>
            <input type="hidden" name="secid" value="' . $secid . '" />';
    }

    echo '</div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-12" for="title">' . adm_translate('Titre') . '</label>
            <div class=" col-12">
                <textarea class="form-control" id="title" name="title" rows="2">' . $title . '</textarea>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-form-label col-12" for="content">' . adm_translate('Contenu') . '</label>
            <div class=" col-12">
                <textarea class="tin form-control" id="content" name="content" rows="30">' . $content . '</textarea>
            </div>
        </div>
        ' . Editeur::affEditeur('content', '');

    droits($userlevel);

    echo $fin;

    echo '</form>';

    Validation::adminFoot('', '', '', '');
}

function secarticleadd($secid, $title, $content, $autho, $members, $Mmembers)
{
    global $radminsuper;

    // pas de removehack pour l'entrée des données ???????
    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);
    }

    $title = stripslashes(Sanitize::fixQuotes($title));

    if ($secid != "0") {
        if ($radminsuper == 1) {
            $timestamp = time();

            $content = stripslashes(Sanitize::fixQuotes(data_image_to_file_url($content, 'modules/upload/storage/s')));

            sql_query("INSERT INTO " . sql_prefix('seccont') . " 
                       VALUES (NULL, '$secid', '$title', '$content', '0', '$autho', '99', '$members', '$timestamp')");

            global $aid;
            Log::ecrireLog('security', sprintf('CreateArticleSections(%s, %s) by AID : %s', $secid, $title, $aid), '');
        } else {
            $content = stripslashes(Sanitize::fixQuotes(data_image_to_file_url($content, 'cache/s')));

            sql_query("INSERT INTO " . sql_prefix('seccont_tempo') . " 
                       VALUES (NULL, '$secid', '$title', '$content', '0', '$autho', '99', '$members')");

            global $aid;
            Log::ecrireLog('security', sprintf('CreateArticleSectionsTempo(%s, %s) by AID : %s', $secid, $title, $aid), '');
        }
    }

    Header('Location: admin.php?op=sections');
}

function secartchange($artid, $secid, $title, $content, $members, $Mmembers)
{
    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);
    }

    $title = stripslashes(Sanitize::fixQuotes($title));
    $content = stripslashes(Sanitize::fixQuotes(data_image_to_file_url($content, 'modules/upload/storage/s')));

    $timestamp = time();

    if ($secid != '0') {
        sql_query("UPDATE " . sql_prefix('seccont') . " 
                   SET secid='$secid', title='$title', content='$content', userlevel='$members', timestamp='$timestamp' 
                   WHERE artid='$artid'");

        global $aid;
        Log::ecrireLog('security', sprintf('UpdateArticleSections(%s, %s, %s) by AID : %s', $artid, $secid, $title, $aid), '');
    }

    Header('Location: admin.php?op=secartedit&artid=' . $artid);
}

function secartchangeup($artid, $secid, $title, $content, $members, $Mmembers)
{
    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);
    }

    $title = stripslashes(Sanitize::fixQuotes($title));
    $content = stripslashes(Sanitize::fixQuotes(data_image_to_file_url($content, 'storage/cache/s')));

    if ($secid != '0') {
        sql_query("UPDATE " . sql_prefix('seccont_tempo') . " 
                   SET secid='$secid', title='$title', content='$content', userlevel='$members' 
                   WHERE artid='$artid'");

        global $aid;
        Log::ecrireLog('security', sprintf('UpdateArticleSectionsTempo(%s, %s, %s) by AID : %s', $artid, $secid, $title, $aid), '');
    }

    Header('Location: admin.php?op=secartupdate&artid=' . $artid);
}

function secartpublish($artid, $secid, $title, $content, $author, $members, $Mmembers)
{
    if (is_array($Mmembers) and ($members == 1)) {
        $members = implode(',', $Mmembers);
    }

    $content = stripslashes(Sanitize::fixQuotes(data_image_to_file_url($content, 'modules/upload/storage/s')));
    $title = stripslashes(Sanitize::fixQuotes($title));

    if ($secid != '0') {
        sql_query("DELETE FROM " . sql_prefix('seccont_tempo') . " 
                   WHERE artid='$artid'");

        $timestamp = time();

        sql_query("INSERT INTO " . sql_prefix('seccont') . " 
                   VALUES (NULL, '$secid', '$title', '$content', '0', '$author', '99', '$members', '$timestamp')");

        global $aid;
        Log::ecrireLog('security', sprintf('PublicateArticleSections(%s, %s, %s) by AID : %s', $artid, $secid, $title, $aid), '');

        $result = sql_query("SELECT email 
                             FROM " . sql_prefix('authors') . " 
                             WHERE aid='$author'");

        list($lemail) = sql_fetch_row($result);

        $sujet = html_entity_decode(adm_translate('Validation de votre publication'), ENT_COMPAT | ENT_HTML401, 'UTF-8');
        $message = adm_translate('La publication que vous aviez en attente vient d\'être validée');

        global $notify_from;
        Mailer::sendEmail($lemail, $sujet, $message, $notify_from, true, 'html', '');
    }

    Header('Location: admin.php?op=sections');
}
// Fonction ARTICLES

// Fonctions de DELETE
function rubriquedelete($rubid, $ok = 0)
{
    // protection
    global $radminsuper;

    if (!$radminsuper) {
        Header('Location: admin.php?op=sections');
    }

    if ($ok == 1) {
        $result = sql_query("SELECT secid 
                             FROM " . sql_prefix('sections') . " 
                             WHERE rubid='$rubid'");

        if (sql_num_rows($result) > 0) {
            while (list($secid) = sql_fetch_row($result)) {

                $result2 = sql_query("SELECT artid 
                                      FROM " . sql_prefix('seccont') . " 
                                      WHERE secid='$secid'");

                if (sql_num_rows($result2) > 0) {
                    while (list($artid) = sql_fetch_row($result2)) {
                        sql_query("DELETE FROM " . sql_prefix('seccont') . " 
                                   WHERE artid='$artid'");

                        sql_query("DELETE FROM " . sql_prefix('compatsujet') . " 
                                   WHERE id1='$artid'");
                    }
                }
            }
        }

        sql_query("DELETE FROM " . sql_prefix('sections') . " 
                   WHERE rubid='$rubid'");

        sql_query("DELETE FROM " . sql_prefix('rubriques') . " 
                   WHERE rubid='$rubid'");

        global $aid;
        Log::ecrireLog('security', sprintf('DeleteRubriques(%s) by AID : %s', $rubid, $aid), '');

        Header('Location: admin.php?op=sections');
    } else {
        global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

        include 'header.php';

        GraphicAdmin($hlpfile);
        adminhead($f_meta_nom, $f_titre, $adminimg);

        $result = sql_query("SELECT rubname 
                             FROM " . sql_prefix('rubriques') . " 
                             WHERE rubid='$rubid'");

        list($rubname) = sql_fetch_row($result);

        echo '<hr />
        <h3 class="mb-3 text-danger">' . adm_translate('Effacer la Rubrique : ') . '<span class="text-body-secondary">' . Language::affLangue($rubname) . '</span></h3>
        <div class="alert alert-danger">
            <strong>' . adm_translate('Etes-vous sûr de vouloir effacer cette Rubrique ?') . '</strong><br /><br />
            <a class="btn btn-danger btn-sm" href="admin.php?op=rubriquedelete&amp;rubid=' . $rubid . '&amp;ok=1" role="button">' . adm_translate('Oui') . '</a>&nbsp;<a class="btn btn-secondary btn-sm" href="admin.php?op=sections" role="button">' . adm_translate('Non') . '</a>
        </div>';

        Validation::adminFoot('', '', '', '');
    }
}

function sectiondelete($secid, $ok = 0)
{
    // protection
    $tmp = droits_publication($secid);

    if (($tmp != 7) and ($tmp != 4)) {
        Header('Location: admin.php?op=sections');
    }

    if ($ok == 1) {
        $result = sql_query("SELECT artid 
                             FROM " . sql_prefix('seccont') . " 
                             WHERE secid='$secid'");

        if (sql_num_rows($result) > 0) {
            while (list($artid) = sql_fetch_row($result)) {
                sql_query("DELETE FROM " . sql_prefix('compatsujet') . " 
                           WHERE id1='$artid'");
            }
        }

        sql_query("DELETE FROM " . sql_prefix('seccont') . " 
                   WHERE secid='$secid'");

        sql_query("DELETE FROM " . sql_prefix('sections') . " 
                   WHERE secid='$secid'");

        global $aid;
        Log::ecrireLog('security', sprintf('DeleteSections(%s) by AID : %s', $secid, $aid), '');

        Header('Location: admin.php?op=sections');
    } else {
        global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

        include 'header.php';

        GraphicAdmin($hlpfile);
        adminhead($f_meta_nom, $f_titre, $adminimg);

        $result = sql_query("SELECT secname 
                             FROM " . sql_prefix('sections') . " 
                             WHERE secid='$secid'");

        list($secname) = sql_fetch_row($result);

        echo '<hr />
        <h3 class="mb-3 text-danger">' . adm_translate('Effacer la sous-rubrique : ') . '<span class="text-body-secondary">' . Language::affLangue($secname) . '</span></h3>
        <div class="alert alert-danger">
            <strong>' . adm_translate('Etes-vous sûr de vouloir effacer cette sous-rubrique ?') . '</strong><br /><br />
            <a class="btn btn-danger btn-sm" href="admin.php?op=sectiondelete&amp;secid=' . $secid . '&amp;ok=1" role="button">' . adm_translate('Oui') . '</a>&nbsp;<a class="btn btn-secondary btn-sm" role="button" href="admin.php?op=sections" >' . adm_translate('Non') . '</a>
        </div>';

        Validation::adminFoot('', '', '', '');
    }
}

function secartdelete($artid, $ok = 0)
{
    // protection
    $result = sql_query("SELECT secid 
                         FROM " . sql_prefix('seccont') . " 
                         WHERE artid='$artid'");

    list($secid) = sql_fetch_row($result);

    $tmp = droits_publication($secid);

    if (($tmp != 7) and ($tmp != 4)) {
        Header('Location: admin.php?op=sections');
    }

    if ($ok == 1) {
        $res = sql_query("SELECT content 
                          FROM " . sql_prefix('seccont') . " 
                          WHERE artid='$artid'");

        list($content) = sql_fetch_row($res);

        $rechuploadimage = '#modules/upload/storage/s\d+_\d+_\d+.[a-z]{3,4}#m';
        preg_match_all($rechuploadimage, $content, $uploadimages);

        foreach ($uploadimages[0] as $imagetodelete) {
            unlink($imagetodelete);
        }

        sql_query("DELETE FROM " . sql_prefix('seccont') . " 
                   WHERE artid='$artid'");

        sql_query("DELETE FROM " . sql_prefix('compatsujet') . " 
                   WHERE id1='$artid'");

        global $aid;
        Log::ecrireLog('security', sprintf('DeleteArticlesSections(%s) by AID : %s', $artid, $aid), '');

        Header('Location: admin.php?op=sections');
    } else {
        global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

        include 'header.php';

        GraphicAdmin($hlpfile);
        adminhead($f_meta_nom, $f_titre, $adminimg);

        $result = sql_query("SELECT title 
                             FROM " . sql_prefix('seccont') . " 
                             WHERE artid='$artid'");

        list($title) = sql_fetch_row($result);

        echo '<hr />
        <h3 class="mb-3 text-danger">' . adm_translate('Effacer la publication :') . ' <span class="text-body-secondary">' . Language::affLangue($title) . '</span></h3>
        <p class="alert alert-danger">
            <strong>' . adm_translate('Etes-vous certain de vouloir effacer cette publication ?') . '</strong><br /><br />
            <a class="btn btn-danger btn-sm" href="admin.php?op=secartdelete&amp;artid=' . $artid . '&amp;ok=1" role="button">' . adm_translate('Oui') . '</a>&nbsp;<a class="btn btn-secondary btn-sm" role="button" href="admin.php?op=sections" >' . adm_translate('Non') . '</a>
        </p>';

        include 'footer.php';
    }
}

function secartdelete2($artid, $ok = 0)
{
    if ($ok == 1) {
        sql_query("DELETE FROM " . sql_prefix('seccont_tempo') . " 
                   WHERE artid='$artid'");

        global $aid;
        Log::ecrireLog('security', sprintf('DeleteArticlesSectionsTempo(%s) by AID : %s', $artid, $aid), '');

        Header('Location: admin.php?op=sections');
    } else {
        global $hlpfile, $f_meta_nom, $f_titre, $adminimg;

        include 'header.php';

        GraphicAdmin($hlpfile);
        adminhead($f_meta_nom, $f_titre, $adminimg);

        $result = sql_query("SELECT title 
                             FROM " . sql_prefix('seccont_tempo') . " 
                             WHERE artid='$artid'");

        list($title) = sql_fetch_row($result);

        echo '<hr />
        <h3 class="mb-3 text-danger">' . adm_translate('Effacer la publication :') . ' <span class="text-body-secondary">' . Language::affLangue($title) . '</span></h3>
        <p class="alert alert-danger">
            <strong>' . adm_translate('Etes-vous certain de vouloir effacer cette publication ?') . '</strong><br /><br />
            <a class="btn btn-danger btn-sm" href="admin.php?op=secartdelete2&amp;artid=' . $artid . '&amp;ok=1" role="button">' . adm_translate('Oui') . '</a>&nbsp;<a class="btn btn-secondary btn-sm" role="button" href="admin.php?op=sections" >' . adm_translate('Non') . '</a>
        </p>';

        include 'footer.php';
    }
}
// Fonctions de DELETE

// Fonctions de classement
function ordremodule()
{
    global $hlpfile, $radminsuper, $f_meta_nom, $f_titre, $adminimg;

    if ($radminsuper <> 1) {
        Header('Location: admin.php?op=sections');
    }

    include 'header.php';

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    //data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-icons-prefix="fa" data-icons="icons"

    echo '<hr />
    <h3 class="mb-3">' . adm_translate('Changer l\'ordre des rubriques') . '</h3>
    <form action="admin.php" method="post" id="ordremodule" name="adminForm">
        <table class="table table-borderless table-sm table-hover table-striped">
            <thead>
                <tr>
                <th data-sortable="true" class="n-t-col-xs-2">' . adm_translate('Index') . '</th>
                <th data-sortable="true" class="n-t-col-xs-10">' . adm_translate('Rubriques') . '</th>
                </tr>
            </thead>
            <tbody>';

    $result = sql_query("SELECT rubid, rubname, ordre 
                         FROM " . sql_prefix('rubriques') . " 
                         ORDER BY ordre");

    //$numrow = sql_num_rows($result); //??

    $i = 0;
    $fv_parametres = '';

    while (list($rubid, $rubname, $ordre) = sql_fetch_row($result)) {
        $i++;

        echo '<tr>
                <td>
                    <div class="mb-3 mb-0">
                        <input type="hidden" name="rubid[' . $i . ']" value="' . $rubid . '" />
                        <input type="text" class="form-control" id="ordre' . $i . '" name="ordre[' . $i . ']" value="' . $ordre . '" maxlength="4" required="required" />
                    </div>
                </td>
                <td><label class="col-form-label" for="ordre' . $i . '">' . Language::affLangue($rubname) . '</label></td>
            </tr>';

        $fv_parametres .= '
            "ordre[' . $i . ']": {
            validators: {
                regexp: {
                regexp:/^\d{1,4}$/,
                message: "0-9"
                }
            }
        },';
    }

    echo '</tbody>
        </table>
        <div class="mb-3 mt-3">
            <input type="hidden" name="i" value="' . $i . '" />
            <input type="hidden" name="op" value="majmodule" />
            <button type="submit" class="btn btn-primary" >' . adm_translate('Valider') . '</button>
            <button class="btn btn-secondary" onclick="javascript:history.back()" >' . adm_translate('Retour en arrière') . '</button>
        </div>
    </form>';

    $arg1 = 'var formulid = ["ordremodule"];';

    Validation::adminFoot('fv', $fv_parametres, $arg1, '');
}

function ordrechapitre()
{
    global $rubname, $rubid, $hlpfile, $radminsuper, $f_meta_nom, $f_titre, $adminimg;

    if ($radminsuper <> 1) {
        Header('Location: admin.php?op=sections');
    }

    include 'header.php';

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    echo '<hr />
    <h3 class="mb-3">' . adm_translate('Changer l\'ordre des sous-rubriques') . ' ' . adm_translate('dans') . ' / <span class="text-body-secondary">' . $rubname . '</span></h3>
    <form action="admin.php" method="post" id="ordrechapitre" name="adminForm">
        <table class="table table-borderless table-sm table-hover table-striped">
            <thead>
                <tr>
                <th data-sortable="true" class="n-t-col-xs-2">' . adm_translate('Index') . '</th>
                <th data-sortable="true" class="n-t-col-xs-10">' . adm_translate('Sous-rubriques') . '</th>
                </tr>
            </thead>
            <tbody>';

    $result = sql_query("SELECT secid, secname, ordre 
                         FROM " . sql_prefix('sections') . " 
                         WHERE rubid='$rubid' 
                         ORDER BY ordre");

    $numrow = sql_num_rows($result);

    $i = 0;
    $fv_parametres = '';

    while (list($secid, $secname, $ordre) = sql_fetch_row($result)) {
        $i++;

        echo '<tr>
            <td>
                <div class="mb-3 mb-0">
                    <input type="hidden" name="secid[' . $i . ']" value="' . $secid . '" />
                    <input type="text" class="form-control" name="ordre[' . $i . ']" id="ordre' . $i . '" value="' . $ordre . '" maxlength="3" required="required" />
                </div>
            </td>
            <td><label class="col-form-label" for="ordre' . $i . '">' . Language::affLangue($secname) . '</label></td>
        </tr>';

        $fv_parametres .= '
            "ordre[' . $i . ']": {
            validators: {
                regexp: {
                regexp:/^\d{1,3}$/,
                message: "0-9"
                },
                between: {
                min: 1,
                max: ' . $numrow . ',
                message: "1 ... ' . $numrow . '"
                }
            }
        },';
    }

    echo '</tbody>
        </table>
        <div class="mb-3 mt-3">
            <input type="hidden" name="op" value="majchapitre" />
            <input type="submit" class="btn btn-primary" value="' . adm_translate('Valider') . '" />
            <button class="btn btn-secondary" onclick="javascript:history.back()" >' . adm_translate('Retour en arrière') . ' </button>
        </div>
    </form>';

    $arg1 = 'var formulid = ["ordrechapitre"];';

    Validation::adminFoot('fv', $fv_parametres, $arg1, '');
}

function ordrecours()
{
    global $secid, $hlpfile, $radminsuper, $f_meta_nom, $f_titre, $adminimg;

    if ($radminsuper <> 1) {
        Header('Location: admin.php?op=sections');
    }

    include 'header.php';

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    $result = sql_query("SELECT secname 
                         FROM " . sql_prefix('sections') . " 
                         WHERE secid='$secid'");

    list($secname) = sql_fetch_row($result);

    echo '<hr />
    <h3 class="mb-3">' . adm_translate('Changer l\'ordre') . ' ' . adm_translate('des') . ' ' . adm_translate('publications') . ' / ' . Language::affLangue($secname) . '</h3>
    <form id="ordrecours" action="admin.php" method="post" name="adminForm">
        <table class="table table-borderless table-sm table-hover table-striped">
            <thead>
                <tr>
                <th data-sortable="true" class="n-t-col-xs-2">' . adm_translate('Index') . '</th>
                <th data-sortable="true" class="n-t-col-xs-10">' . adm_translate('Publications') . '</th>
                </tr>
            </thead>
            <tbody>';

    $result = sql_query("SELECT artid, title, ordre 
                         FROM " . sql_prefix('seccont') . " 
                         WHERE secid='$secid' 
                         ORDER BY ordre");

    $numrow = sql_num_rows($result);

    $i = 0;
    $fv_parametres = '';

    while (list($artid, $title, $ordre) = sql_fetch_row($result)) {
        $i++;

        echo '<tr>
            <td>
                <div class="mb-3 mb-0">
                    <input type="hidden" name="artid[' . $i . ']" value="' . $artid . '" />
                    <input type="text" class="form-control" id="ordre' . $i . '" name="ordre[' . $i . ']" value="' . $ordre . '"  maxlength="4" required="required" />
                </div>
            </td>
            <td><label class="col-form-label" for="ordre' . $i . '">' . Language::affLangue($title) . '</label></td>
            </tr>';

        $fv_parametres .= '
            "ordre[' . $i . ']": {
            validators: {
                regexp: {
                regexp:/^\d{1,4}$/,
                message: "0-9"
                },
                between: {
                min: 1,
                max: ' . $numrow . ',
                message: "1 ... ' . $numrow . '"
                }
            }
        },';
    }

    echo '</tbody>
        </table>
        <div class="mb-3 mt-3">
            <input type="hidden" name="op" value="majcours" />
            <input type="submit" class="btn btn-primary" value="' . adm_translate('Valider') . '" />
            <input type="button" class="btn btn-secondary" value="' . adm_translate('Retour en arrière') . '" onclick="javascript:history.back()" />
        </div>
    </form>';

    $arg1 = 'var formulid = ["ordrecours"];';

    Validation::adminFoot('fv', $fv_parametres, $arg1, '');
}

function updateordre($rubid, $artid, $secid, $op, $ordre)
{
    global $radminsuper;

    if ($radminsuper != 1) {
        Header('Location: admin.php?op=sections');
    }

    if ($op == 'majmodule') {
        $i = count($rubid);

        for ($j = 1; $j < ($i + 1); $j++) {
            $rub = $rubid[$j];
            $ord = $ordre[$j];

            $result = sql_query("UPDATE " . sql_prefix('rubriques') . " 
                                 SET ordre='$ord' 
                                 WHERE rubid='$rub'");
        }
    }

    if ($op == 'majchapitre') {
        $i = count($secid);

        for ($j = 1; $j < ($i + 1); $j++) {
            $sec = $secid[$j];
            $ord = $ordre[$j];

            $result = sql_query("UPDATE " . sql_prefix('sections') . " 
                                 SET ordre='$ord' 
                                 WHERE secid='$sec'");
        }
    }

    if ($op == 'majcours') {
        $i = count($artid);

        for ($j = 1; $j < ($i + 1); $j++) {
            $art = $artid[$j];
            $ord = $ordre[$j];

            $result = sql_query("UPDATE " . sql_prefix('seccont') . " 
                                 SET ordre='$ord' 
                                 WHERE artid='$art'");
        }
    }

    Header('Location: admin.php?op=sections');
}
// Fonctions de classement

// Fonctions DROIT des AUTEURS
function publishrights($author)
{
    global $hlpfile, $radminsuper, $f_meta_nom, $f_titre, $adminimg;

    if ($radminsuper != 1) {
        Header('Location: admin.php?op=sections');
    }

    include 'header.php';

    GraphicAdmin($hlpfile);
    adminhead($f_meta_nom, $f_titre, $adminimg);

    echo '<hr />
    <h3 class="mb-3"><i class="fa fa-user-edit me-2"></i>' . adm_translate('Droits des auteurs') . ' : <span class="text-body-secondary">' . $author . '</span></h3>
    <form action="admin.php" method="post">';

    $result1 = sql_query("SELECT rubid, rubname 
                          FROM " . sql_prefix('rubriques') . " 
                          ORDER BY ordre");

    //$numrow = sql_num_rows($result1); // ??

    $i = 0;
    $scrr = '';
    $scrsr = '';

    while (list($rubid, $rubname) = sql_fetch_row($result1)) {
        echo '<table class="table table-bordered table-sm" data-toggle="" data-classes=""  data-striped="true" data-icons-prefix="fa" data-icons="icons">
                <thead class="thead-light">
                <tr class="table-secondary"><th colspan="5"><span class="form-check"><input class="form-check-input" id="ckbrall_' . $rubid . '" type="checkbox" /><label class="form-check-label lead" for="ckbrall_' . $rubid . '">' . Language::affLangue($rubname) . '</label></span></th></tr>
                <tr class="">
                    <th class="colspan="2" n-t-col-xs-3" data-sortable="true">' . adm_translate('Sous-rubriques') . '</th>
                    <th class="n-t-col-xs-2 text-center" data-halign="center" data-align="center">' . adm_translate('Créer') . '</th>
                    <th class="n-t-col-xs-2 text-center" data-halign="center" data-align="center">' . adm_translate('Publier') . '</th>
                    <th class="n-t-col-xs-2 text-center" data-halign="center" data-align="center">' . adm_translate('Modifier') . '</th>
                    <th class="n-t-col-xs-2 text-center" data-halign="center" data-align="center">' . adm_translate('Supprimer') . '</th>
                </tr>
                </thead>
                <tbody>';

        $scrr .= '$("#ckbrall_' . $rubid . '").change(function(){
                    $(".ckbr_' . $rubid . '").prop("checked", $(this).prop("checked"));
                });';

        $result2 = sql_query("SELECT secid, secname 
                              FROM " . sql_prefix('sections') . " 
                              WHERE rubid='$rubid' 
                              ORDER BY ordre");

        while (list($secid, $secname) = sql_fetch_row($result2)) {
            $result3 = sql_query("SELECT type 
                                  FROM " . sql_prefix('publisujet') . " 
                                  WHERE secid2='$secid' 
                                  AND aid='$author'");

            $i++;

            $crea = '';
            $publi = '';
            $modif = '';
            $supp = '';

            if (sql_num_rows($result3) > 0) {
                while (list($type) = sql_fetch_row($result3)) {
                    if ($type == 1) {
                        $crea = 'checked="checked"';
                    } else if ($type == 2) {
                        $publi = 'checked="checked"';
                    } else if ($type == 3) {
                        $modif = 'checked="checked"';
                    } else if ($type == 4) {
                        $supp = 'checked="checked"';
                    }
                }
            }

            echo '<tr>
                    <td><div class="form-check"><input class="form-check-input" id="ckbsrall_' . $secid . '" type="checkbox" /><label class="form-check-label" for="ckbsrall_' . $secid . '">' . Language::affLangue($secname) . '</label></div></td>
                    <td class="text-center"><div class="form-check"><input class="form-check-input ckbsr_' . $secid . ' ckbr_' . $rubid . '" type="checkbox" id="creation' . $i . '" name="creation[' . $i . ']" value="' . $secid . '" ' . $crea . ' /><label class="form-check-label" for="creation' . $i . '"></label></div></td>
                    <td class="text-center"><div class="form-check"><input class="form-check-input ckbsr_' . $secid . ' ckbr_' . $rubid . '" type="checkbox" id="publication' . $i . '" name="publication[' . $i . ']" value="' . $secid . '" ' . $publi . ' /><label class="form-check-label" for="publication' . $i . '"></label></div></td>
                    <td class="text-center"><div class="form-check"><input class="form-check-input ckbsr_' . $secid . ' ckbr_' . $rubid . '" type="checkbox" id="modification' . $i . '" name="modification[' . $i . ']" value="' . $secid . '" ' . $modif . ' /><label class="form-check-label" for="modification' . $i . '"></label></div></td>
                    <td class="text-center"><div class="form-check"><input class="form-check-input ckbsr_' . $secid . ' ckbr_' . $rubid . '" type="checkbox" id="suppression' . $i . '" name="suppression[' . $i . ']" value="' . $secid . '" ' . $supp . ' /><label class="form-check-label" for="suppression' . $i . '"></label></div></td>
                </tr>';

            $scrsr .= '$("#ckbsrall_' . $secid . '").change(function(){
                    $(".ckbsr_' . $secid . '").prop("checked", $(this).prop("checked"));
                });';
        }

        echo '</tbody>
            </table>
        <br />';
    }

    echo '<input type="hidden" name="chng_aid" value="' . $author . '" />
            <input type="hidden" name="op" value="updatedroitauteurs" />
            <input type="hidden" name="maxindex" value="' . $i . '" />
            <input class="btn btn-primary me-3" type="submit" value="' . adm_translate('Valider') . '" />
            <input class="btn btn-secondary" type="button" onclick="javascript:history.back()" value="' . adm_translate('Retour en arrière') . '" />
    </form>';

    echo '<script type="text/javascript">
        //<![CDATA[
            $(document).ready(function(){
            ' . $scrr . $scrsr . '
            });
        //]]>
    </script>';

    Validation::adminFoot('', '', '', '');
}

function droitsalacreation($chng_aid, $secid)
{
    $lesdroits = array('1', '2', '3');

    // if($secid > 0) {
    foreach ($lesdroits as $droit) {
        sql_query("INSERT INTO " . sql_prefix('publisujet') . " 
                   VALUES ('$chng_aid', '$secid', '$droit')");
    }
    // } else {
    //    sql_query("INSERT INTO ".sql_prefix('publisujet')." 
    //               VALUES ('$chng_aid', '$secid', '1')");
    // }
}

function updaterights($chng_aid, $maxindex, $creation, $publication, $modification, $suppression)
{
    global $radminsuper;

    if ($radminsuper != 1) {
        Header('Location: admin.php?op=sections');
    }

    $result = sql_query("DELETE FROM " . sql_prefix('publisujet') . " 
                         WHERE aid='$chng_aid'");

    for ($j = 1; $j < ($maxindex + 1); $j++) {
        if (array_key_exists($j, $creation))
            if ($creation[$j] != '') {
                $result = sql_query("INSERT INTO " . sql_prefix('publisujet') . " 
                                     VALUES ('$chng_aid', '$creation[$j]', '1')");
            }

        if (array_key_exists($j, $publication))
            if ($publication[$j] != '') {
                $result = sql_query("INSERT INTO " . sql_prefix('publisujet') . " 
                                     VALUES ('$chng_aid', '$publication[$j]', '2')");
            }

        if (array_key_exists($j, $modification))
            if ($modification[$j] != '') {
                $result = sql_query("INSERT INTO " . sql_prefix('publisujet') . " 
                                     VALUES ('$chng_aid', '$modification[$j]', '3')");
            }

        if (array_key_exists($j, $suppression))
            if ($suppression[$j] != '') {
                $result = sql_query("INSERT INTO " . sql_prefix('publisujet') . " 
                                     VALUES ('$chng_aid', '$suppression[$j]', '4')");
            }
    }

    global $aid;
    Log::ecrireLog('security', sprintf('UpdateRightsPubliSujet(%s) by AID : %s', $chng_aid, $aid), '');

    Header('Location: admin.php?op=sections');
}
// Fonctions DROIT des AUTEURS

settype($Mmembers, 'array');
settype($suppression, 'array');
settype($modification, 'array');
settype($publication, 'array');
settype($ok, 'integer');

switch ($op) {

    case 'new_rub_section':
        new_rub_section($type);
        break;

    case 'sections':
        sections();
        break;

    case 'sectionedit':
        sectionedit($secid);
        break;

    case 'sectionmake':
        sectionmake($secname, $image, $members, $Mmembers, $rubref, $introd);
        break;

    case 'sectiondelete':
        sectiondelete($secid, $ok);
        break;

    case 'sectionchange':
        sectionchange($secid, $secname, $image, $members, $Mmembers, $rubref, $introd);
        break;

    case 'rubriquedit':
        rubriquedit($rubid);
        break;

    case 'rubriquemake':
        rubriquemake($rubname, $introc);
        break;

    case 'rubriquedelete':
        rubriquedelete($rubid, $ok);
        break;

    case 'rubriquechange':
        rubriquechange($rubid, $rubname, $introc, $enligne);
        break;

    case 'secarticleadd':
        secarticleadd($secid, $title, $content, $autho, $members, $Mmembers);
        break;

    case 'secartedit':
        secartedit($artid);
        break;

    case 'secartchange':
        secartchange($artid, $secid, $title, $content, $members, $Mmembers);
        break;

    case 'secartchangeup':
        secartchangeup($artid, $secid, $title, $content, $members, $Mmembers);
        break;

    case 'secartdelete':
        secartdelete($artid, $ok);
        break;

    case 'secartpublish':
        secartpublish($artid, $secid, $title, $content, $author, $members, $Mmembers);
        break;

    case 'secartupdate':
        secartupdate($artid);
        break;

    case 'secartdelete2':
        secartdelete2($artid, $ok);
        break;

    case 'ordremodule':
        ordremodule();
        break;

    case 'ordrechapitre':
        ordrechapitre();
        break;

    case 'ordrecours':
        ordrecours();
        break;

    case 'majmodule':
        updateordre($rubid, '', '', $op, $ordre);
        break;

    case 'majchapitre':
        updateordre('', '', $secid, $op, $ordre);
        break;

    case 'majcours':
        updateordre('', $artid, '', $op, $ordre);
        break;

    case 'publishcompat':
        publishcompat($article);
        break;

    case 'updatecompat':
        updatecompat($article, $admin_rub, $idx);
        break;

    case 'droitauteurs':
        publishrights($author);
        break;

    case 'updatedroitauteurs':
        updaterights($chng_aid, $maxindex, $creation, $publication, $modification, $suppression);
        break;
}
