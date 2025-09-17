<?php

namespace App\Http\Controllers\Admin\News;


use IntlDateFormatter;
use App\Support\Facades\Date;
use App\Support\Facades\User;
use App\Support\Facades\Language;
use App\Support\Facades\Validation;
use App\Http\Controllers\Core\AdminBaseController;


class Submissions extends AdminBaseController
{
    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        //$f_meta_nom = 'submissions';
        //$f_titre = adm_translate('Article en attente de validation');

        // controle droit
        //admindroits($aid, $f_meta_nom);

        //global $language;
        //$hlpfile = 'admin/manuels/' . $language . '/submissions.html';

        /*
        switch ($op) {

            default:
                $this->submissions();
                break;
        }

        // NEWS
        case 'submissions':
            include 'admin/submissions.php';
            break;

        */

        parent::initialize();        
    }

    public function submissions()
    {
        global $aid, $radminsuper;

        $dummy = 0;

        //include 'header.php';

        //GraphicAdmin($hlpfile);
        //adminhead($f_meta_nom, $f_titre, $adminimg);

        $result = sql_query("SELECT qid, subject, timestamp, topic, uname 
                            FROM " . sql_prefix('queue') . " 
                            ORDER BY timestamp");

        if (sql_num_rows($result) == 0) {
            echo '<hr />
            <h3>' . adm_translate('Pas de nouveaux Articles postés') . '</h3>';
        } else {
            echo '<hr />
            <h3>' . adm_translate('Nouveaux Articles postés') . '<span class="badge bg-danger float-end">' . sql_num_rows($result) . '</span></h3>
            <table id="tad_subm" data-toggle="table" data-striped="true" data-show-toggle="true" data-search="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
                <thead>
                    <tr>
                        <th data-halign="center"><i class="fa fa-user fa-lg"></i></th>
                        <th data-sortable="true" data-sorter="htmlSorter" data-halign="center">' . adm_translate('Sujet') . '</th>
                        <th data-sortable="true" data-sorter="htmlSorter" data-halign="center">' . adm_translate('Titre') . '</th>
                        <th data-halign="center" data-align="right">' . adm_translate('Date') . '</th>
                        <th class="n-t-col-xs-2" data-halign="center" data-align="center">' . adm_translate('Fonctions') . '</th>
                    </tr>
                </thead>
                <tbody>';

            while (list($qid, $subject, $timestamp, $topic, $uname) = sql_fetch_row($result)) {

                if ($topic < 1) {
                    $topic = 1;
                }

                $affiche = false;

                $result2 = sql_query("SELECT topicadmin, topictext, topicimage 
                                    FROM " . sql_prefix('topics') . " 
                                    WHERE topicid='$topic'");

                list($topicadmin, $topictext, $topicimage) = sql_fetch_row($result2);

                if ($radminsuper) {
                    $affiche = true;
                } else {
                    $topicadminX = explode(',', $topicadmin);

                    for ($i = 0; $i < count($topicadminX); $i++) {
                        if (trim($topicadminX[$i]) == $aid) {
                            $affiche = true;
                        }
                    }
                }

                echo '<tr>
                    <td>' . User::userPopover($uname, '40', 2) . ' ' . $uname . '</td>
                    <td>';

                if ($subject == '') {
                    $subject = adm_translate('Aucun Sujet');
                }

                $subject = Language::affLangue($subject);

                if ($affiche) {
                    echo '<img class=" " src="assets/images/topics/' . $topicimage . '" height="30" width="30" alt="avatar" />&nbsp;<a href="admin.php?op=topicedit&amp;topicid=' . $topic . '" class="adm_tooltip">' . Language::affLangue($topictext) . '</a></td>
                    <td align="left"><a href="admin.php?op=DisplayStory&amp;qid=' . $qid . '">' . ucfirst($subject) . '</a></td>';
                } else {
                    echo Language::affLangue($topictext) . '</td>
                    <td><i>' . ucfirst($subject) . '</i></td>';
                }

                echo '<td class="small">' . Date::formatTimes($timestamp, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT) . '</td>';

                if ($affiche) {
                    echo '<td><a class="" href="admin.php?op=DisplayStory&amp;qid=' . $qid . '"><i class="fa fa-edit fa-lg" title="' . adm_translate('Editer') . '" data-bs-toggle="tooltip" ></i></a><a class="text-danger" href="admin.php?op=DeleteStory&amp;qid=' . $qid . '"><i class="fas fa-trash fa-lg ms-3" title="' . adm_translate('Effacer') . '" data-bs-toggle="tooltip" ></i></a></td>
                    </tr>';
                } else {
                    echo '<td>&nbsp;</td>
                    </tr>';
                }

                $dummy++;
            }

            if ($dummy < 1) {
                echo '<h3>' . adm_translate('Pas de nouveaux Articles postés') . '</h3>';
            } else {
                echo '</tbody>
                </table>';
            }
        }

        Validation::adminFoot('', '', '', '');
    }

}
