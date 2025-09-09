<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Library\Url\Url;
use App\Library\Stat\Stat;
use App\Library\Error\Error;
use App\Library\Forum\Forum;
use App\Library\String\Sanitize;
use App\Library\Validation\Validation;
use App\Http\Controllers\Core\AdminBaseController;

class AblaLog extends AdminBaseController
{

    public function __construct()
    {
        //

        // $f_meta_nom = 'abla';
        // $f_titre = translate('Tableau de bord');

        // controle droit
        // admindroits($aid, $f_meta_nom);
    }

    public function index()
    {
        global $admin; // global a revoir !

        if ($admin) {
            // include 'header.php';

            // global $language;
            // $hlpfile = '/admin/manuels/' . $language . '/abla.html';

            // GraphicAdmin($hlpfile);
            // adminhead($f_meta_nom, $f_titre, $adminimg);

            // global $startdate;
            
            list($membres, $totala, $totalb, $totalc, $totald, $totalz) = Stat::reqStat();

            // LNL Email in outside table
            $result = sql_query("SELECT email 
                                FROM " . sql_prefix('lnl_outside_users'));

            if ($result) {
                $totalnl = sql_num_rows($result);
            } else {
                $totalnl = "0";
            }

            include storage_path('abla/log.php');

            $timex = time() - $xdate;

            if ($timex >= 86400) {
                $timex = round($timex / 86400) . ' ' . translate('Jour(s)');
            } elseif ($timex >= 3600) {
                $timex = round($timex / 3600) . ' ' . translate('Heure(s)');
            } elseif ($timex >= 60) {
                $timex = round($timex / 60) . ' ' . translate('Minute(s)');
            } else {
                $timex = $timex . ' ' . translate('Seconde(s)');
            }

            echo '<hr />
            <p class="lead mb-3">' . translate('Statistiques générales') . ' - ' . translate('Dernières stats') . ' : ' . $timex . ' </p>
            <table class="mb-2" data-toggle="table" data-classes="table mb-2">
                <thead class="collapse thead-default">
                    <tr>
                        <th class="n-t-col-xs-9"></th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>' . translate('Nb. pages vues') . ' : </td>
                        ' . $this->row_span($totalz, $xtotalz) . '
                    </tr>
                    <tr>
                        <td>' . translate('Nb. de membres') . ' : </td>
                        ' . $this->row_span($membres, $xmembres) . '
                    </tr>
                    <tr>
                        <td>' . translate('Nb. d\'articles') . ' : </td>
                        ' . $this->row_span($totala, $xtotala) . '
                    </tr>
                    <tr>
                        <td>' . translate('Nb. de forums') . ' : </td>
                        ' . $this->row_span($totalc, $xtotalc) . '
                    </tr>
                    <tr>
                        <td>' . translate('Nb. de sujets') . ' : </td>
                        ' . $this->row_span($totald, $xtotald) . '
                    </tr>
                    <tr>
                        <td>' . translate('Nb. de critiques') . ' : </td>
                        ' . $this->row_span($totalb, $xtotalb) . '
                    </tr>
                    <tr>
                        <td>' . translate('Nb abonnés à lettre infos') . ' : </td>
                        ' . $this->row_span($totalnl, $xtotalnl) . '
                    </tr>';

            $xfile = "<?php\n";
            $xfile .= "\$xdate = " . time() . ";\n";
            $xfile .= "\$xtotalz = $totalz;\n";
            $xfile .= "\$xmembres = $membres;\n";
            $xfile .= "\$xtotala = $totala;\n";
            $xfile .= "\$xtotalc = $totalc;\n";
            $xfile .= "\$xtotald = $totald;\n";
            $xfile .= "\$xtotalb = $totalb;\n";
            $xfile .= "\$xtotalnl = $totalnl;\n";

            echo '</tbody>
            </table>
            <p class="lead my-3">' . translate('Statistiques des chargements') . '</p>
            <table data-toggle="table" data-classes="table">
                <thead class=" thead-default">
                    <tr>
                        <th class="n-t-col-xs-9"></th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>';

            $num_dow = 0;

            $result = sql_query("SELECT dcounter, dfilename 
                                FROM " . sql_prefix('downloads'));

            settype($xdownload, 'array');

            while (list($dcounter, $dfilename) = sql_fetch_row($result)) {
                $num_dow++;

                echo '<tr>
                <td><span class="text-danger">';

                if (array_key_exists($num_dow, $xdownload)) {
                    echo $xdownload[$num_dow][1];
                }

                echo '</span> -/- ' . $dfilename . '</td>
                <td><span class="text-danger">';

                if (array_key_exists($num_dow, $xdownload)) {
                    echo $xdownload[$num_dow][2];
                }

                echo '</span> -/- ' . $dcounter . '</td>
                </tr>';

                $xfile .= "\$xdownload[$num_dow][1] = \"$dfilename\";\n";
                $xfile .= "\$xdownload[$num_dow][2] = \"$dcounter\";\n";
            }

            echo '</tbody>
            </table>
            <p class="lead my-3">Forums</p>
            <table class="table table-bordered table-sm" data-classes="table">
                <thead class="">
                    <tr>
                        <th>' . translate('Forum') . '</th>
                        <th class="n-t-col-xs-2 text-center">' . translate('Sujets') . '</th>
                        <th class="n-t-col-xs-2 text-center">' . translate('Contributions') . '</th>
                        <th class="n-t-col-xs-3 text-end">' . translate('Dernières contributions') . '</th>
                    </tr>
                </thead>';

            $result = sql_query("SELECT * 
                                FROM " . sql_prefix('catagories') . " 
                                ORDER BY cat_id");

            $num_for = 0;

            while (list($cat_id, $cat_title) = sql_fetch_row($result)) {
                $sub_sql = "SELECT f.*, u.uname 
                            FROM " . sql_prefix('forums') . " f, " . sql_prefix('users') . " u 
                            WHERE f.cat_id = '$cat_id' 
                            AND f.forum_moderator = u.uid 
                            ORDER BY forum_index, forum_id";

                if (!$sub_result = sql_query($sub_sql)) {
                    Error::forumError('0022');
                }

                if ($myrow = sql_fetch_assoc($sub_result)) {
                    echo '<tbody>
                    <tr>
                    <td class="table-active" colspan="4">' . stripslashes($cat_title) . '</td>
                    </tr>';

                    do {
                        $num_for++;

                        $last_post = Forum::getLastPost($myrow['forum_id'], 'forum', 'infos', true);

                        echo '<tr>';

                        $total_topics = Forum::getTotalTopics($myrow['forum_id']);

                        $name = stripslashes($myrow['forum_name']);
                        $xfile .= "\$xforum[$num_for][1] = \"$name\";\n";
                        $xfile .= "\$xforum[$num_for][2] = $total_topics;\n";
                        $desc = stripslashes($myrow['forum_desc']);

                        echo '<td>
                        <a tabindex="0" role="button" data-bs-trigger="focus" data-bs-toggle="popover" data-bs-placement="right" data-bs-content="' . $desc . '">
                            <i class="far fa-lg fa-file-alt me-2"></i>
                        </a>
                        <a href="viewforum.php?forum=' . $myrow['forum_id'] . '" >
                            <span class="text-danger">';

                        if (array_key_exists($num_for, $xforum)) {
                            echo $xforum[$num_for][1];
                        }

                        echo '</span> -/- ' . $name . ' </a></td>
                        <td class="text-center"><span class="text-danger">';

                        if (array_key_exists($num_for, $xforum)) {
                            echo $xforum[$num_for][2];
                        }

                        echo '</span> -/- ' . $total_topics . '</td>';

                        $total_posts = Forum::getTotalPosts($myrow['forum_id'], "", "forum", false);
                        $xfile .= "\$xforum[$num_for][3] = $total_posts;\n";

                        echo '<td class="text-center"><span class="text-danger">';

                        if (array_key_exists($num_for, $xforum)) {
                            echo $xforum[$num_for][3];
                        }

                        echo '</span> -/- ' . $total_posts . '</td>
                        <td class="text-end small">' . $last_post . '</td>';
                    } while ($myrow = sql_fetch_assoc($sub_result));
                }
            }

            echo '</tr>
                </tbody>
            </table>';

            $file = fopen(storage_path('abla/log.php'), 'w');
            $xfile .= "?>\n";

            fwrite($file, $xfile);
            fclose($file);

            Validation::adminFoot('', '', '', '');
        } else {
            Url::redirectUrl('index.php');
        }
    }

    private function row_span($total, $xtotal)
    {
        $content = '<td>' . Sanitize::wrh($total) . ' (';

        if ($total > $xtotal) {
            $content .= '<span class="text-success">+';
        } elseif ($total < $xtotal) {
            $content .= '<span class="text-danger">';
        } else {
            $content .= '<span>';
        }

        $content .= Sanitize::wrh($total - $xtotal) . '</span>)</td>';

        return $content;
    }


}
