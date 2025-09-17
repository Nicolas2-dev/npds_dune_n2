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
        parent::initialize();        
    }

    // controller Admin Home !
    function adminMain($deja_affiches)
    {
        global $language, $admart, $hlpfile, $aid, $admf_ext;

        $hlpfile = 'manuels/' . $language . '/admin.html';

        include 'header.php';
        include_once 'functions.php';

        global $short_menu_admin;

        $short_menu_admin = false;

        $radminsuper = GraphicAdmin($hlpfile);

        echo '
        <div id="adm_men_art" class="adm_workarea">
            <h2><img src="assets/images/admin/submissions.' . $admf_ext . '" class="adm_img" title="' . adm_translate('Articles') . '" alt="icon_' . adm_translate('Articles') . '" />&nbsp;' . adm_translate('Derniers') . ' ' . $admart . ' ' . adm_translate('Articles') . '</h2>';

        $resul = sql_query("SELECT sid FROM " . sql_prefix('stories'));
        $nbre_articles = sql_num_rows($resul);

        settype($deja_affiches, 'integer');
        settype($admart, 'integer');

        $result = sql_query("SELECT sid, title, hometext, topic, informant, time, archive, catid, ihome 
                            FROM " . sql_prefix('stories') . " 
                            ORDER BY sid DESC 
                            LIMIT $deja_affiches, $admart");

        $nbPages = ceil($nbre_articles / $admart);
        $current = 1;

        if ($deja_affiches >= 1) {
            $current = $deja_affiches / $admart;
        } else if ($deja_affiches < 1) {
            $current = 0;
        } else {
            $current = $nbPages;
        }

        $start = ($current * $admart);

        if ($nbre_articles) {
            echo '<table id ="lst_art_adm" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-buttons-class="outline-secondary" data-mobile-responsive="true" data-icons-prefix="fa" data-icons="icons">
                <thead>
                    <tr>
                    <th data-sortable="true" data-halign="center" data-align="right" class="n-t-col-xs-1">ID</th>
                    <th data-halign="center" data-sortable="true" data-sorter="htmlSorter" class="n-t-col-xs-5">' . adm_translate('Titre') . '</th>
                    <th data-sortable="true" data-halign="center" class="n-t-col-xs-4">' . adm_translate('Sujet') . '</th>
                    <th data-halign="center" data-align="center" class="n-t-col-xs-2">' . adm_translate('Fonctions') . '</th>
                    </tr>
                </thead>
                <tbody>';

            $i = 0;

            while ((list($sid, $title, $hometext, $topic, $informant, $time, $archive, $catid, $ihome) = sql_fetch_row($result)) and ($i < $admart)) {
                $affiche = false;

                $result2 = sql_query("SELECT topicadmin, topictext, topicimage 
                                    FROM " . sql_prefix('topics') . " 
                                    WHERE topicid='$topic'");

                list($topicadmin, $topictext, $topicimage) = sql_fetch_row($result2);

                $result3 = sql_query("SELECT title 
                                    FROM " . sql_prefix('stories_cat') . " 
                                    WHERE catid='$catid'");

                list($cat_title) = sql_fetch_row($result3);

                if ($radminsuper) {
                    $affiche = true;
                } else {
                    $topicadminX = explode(',', $topicadmin);

                    for ($iX = 0; $iX < count($topicadminX); $iX++) {
                        if (trim($topicadminX[$iX]) == $aid) {
                            $affiche = true;
                        }
                    }
                }

                $hometext = strip_tags($hometext, '<br><br />');
                $lg_max = 200;

                if (strlen($hometext) > $lg_max) {
                    $hometext = substr($hometext, 0, $lg_max) . ' ...';
                }

                echo '<tr>
                    <td>' . $sid . '</td>
                    <td>';

                $title = Language::affLangue($title);

                if ($archive) {
                    echo $title . ' <i>(archive)</i>';
                } else {
                    if ($affiche) {
                        echo '<a data-bs-toggle="popover" data-bs-placement="left" data-bs-trigger="hover" href="article.php?sid=' . $sid . '" data-bs-content=\'   <div class="thumbnail"><img class="img-rounded" src="assets/images/topics/' . $topicimage . '" height="80" width="80" alt="topic_logo" /><div class="caption">' . htmlentities($hometext, ENT_QUOTES) . '</div></div>\' title="' . $sid . '" data-bs-html="true">' . ucfirst($title) . '</a>';

                        if ($ihome == 1) {
                            echo '<br /><small><span class="badge bg-secondary" title="' . adm_translate('Catégorie') . '" data-bs-toggle="tooltip">' . Language::affLangue($cat_title) . '</span> <span class="text-danger">non publié en index</span></small>';
                        } else {
                            if ($catid > 0) {
                                echo '<br /><small><span class="badge bg-secondary" title="' . adm_translate('Catégorie') . '" data-bs-toggle="tooltip"> ' . Language::affLangue($cat_title) . '</span> <span class="text-success"> publié en index</span></small>';
                            }
                        }
                    } else {
                        echo '<i>' . $title . '</i>';
                    }
                }

                if ($topictext == '') {
                    echo '</td>
                    <td>';
                } else {
                    echo '</td>
                    <td>' . $topictext . '<a href="index.php?op=newtopic&amp;topic=' . $topic . '" class="tooltip">' . Language::affLangue($topictext) . '</a>';
                }

                if ($affiche) {
                    echo '</td>
                    <td>
                    <a href="admin.php?op=EditStory&amp;sid=' . $sid . '" ><i class="fas fa-edit fa-lg me-2" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip"></i></a>
                    <a href="admin.php?op=RemoveStory&amp;sid=' . $sid . '" ><i class="fas fa-trash fa-lg text-danger" title="' . adm_translate('Effacer') . '" data-bs-toggle="tooltip"></i></a>';
                } else {
                    echo '</td>
                    <td>';
                }

                echo '</td>
                </tr>';

                $i++;
            }

            echo '</tbody>
            </table>
            <div class="d-flex my-2 justify-content-between flex-wrap">
            <ul class="pagination pagination-sm">
                <li class="page-item disabled"><a class="page-link" href="#">' . $nbre_articles . ' ' . adm_translate('Articles') . '</a></li>
                <li class="page-item disabled"><a class="page-link" href="#">' . $nbPages . ' ' . adm_translate('Page(s)') . '</a></li>
            </ul>';

            echo Paginator::paginate('admin.php?op=suite_articles&amp;deja_affiches=', '', $nbPages, $current, 1, $admart, $start);

            echo '</div>';

            if ($affiche) {
                echo '<form id="fad_articles" class="form-inline" action="admin.php" method="post">
                    <label class="me-2 mt-sm-1">' . adm_translate('ID Article:') . '</label>
                    <input class="form-control  me-2 mt-sm-3 mb-2" type="number" name="sid" />
                    <select class="form-select me-2 mt-sm-3 mb-2" name="op">
                        <option value="EditStory" selected="selected">' . adm_translate('Editer un Article') . '</option>
                        <option value="RemoveStory">' . adm_translate('Effacer l\'Article') . '</option>
                    </select>
                    <button class="btn btn-primary ms-sm-2 mt-sm-3 mb-2" type="submit">' . adm_translate('Ok') . ' </button>
                </form>';
            }
        }

        echo '</div>';

        include 'footer.php';
    }

}
