<?php

namespace App\Library\Download\Traits;

use IntlDateFormatter;
use App\Support\Sanitize;
use App\Support\Facades\Auth;
use App\Support\Facades\Date;
use App\Support\Facades\Language;
use App\Library\FileManagement\FileManagement;


trait DownloadTrait 
{

    public function genInfo(int $did, int $out_template)
    {
        $result = sql_query("SELECT dcounter, durl, dfilename, dfilesize, ddate, dweb, duser, dver, dcategory, ddescription, perms 
                            FROM " . sql_prefix('downloads') . " 
                            WHERE did='$did'");

        list($dcounter, $durl, $dfilename, $dfilesize, $ddate, $dweb, $duser, $dver, $dcategory, $ddescription, $dperm) = sql_fetch_row($result);

        $okfile = false;

        if (!stristr($dperm, ',')) {
            $okfile = Auth::autorisation($dperm);
        } else {
            $ibidperm = explode(',', $dperm);

            foreach ($ibidperm as $v) {
                if (Auth::autorisation($v)) {
                    $okfile = true;
                    break;
                }
            }
        }

        if ($okfile) {
            if ($out_template == 1) {
                echo '<h2 class="mb-3">' . translate('Chargement de fichiers') . '</h2>
                <div class="card">
                    <div class="card-header"><h4>' . $dfilename . '<span class="ms-3 text-body-secondary small">@' . $durl . '</h4></div>
                    <div class="card-body">';
            }

            echo '<p><strong>' . translate('Taille du fichier') . ' : </strong>';

            $objZF = new FileManagement;

            echo ($dfilesize != 0) ? $objZF->fileSizeFormat($dfilesize, 1) : $objZF->fileSizeAuto($durl, 2);

            echo '</p>
                <p><strong>' . translate('Version') . '&nbsp;:</strong>&nbsp;' . $dver . '</p>
                <p><strong>' . translate('Date de chargement sur le serveur') . '&nbsp;:</strong>&nbsp;' . Date::formatTimes($ddate, IntlDateFormatter::SHORT, IntlDateFormatter::NONE) . '</p>
                <p><strong>' . translate('Chargements') . '&nbsp;:</strong>&nbsp;' . Sanitize::wrh($dcounter) . '</p>
                <p><strong>' . translate('Catégorie') . '&nbsp;:</strong>&nbsp;' . Language::affLangue(stripslashes($dcategory)) . '</p>
                <p><strong>' . translate('Description') . '&nbsp;:</strong>&nbsp;' . Language::affLangue(stripslashes($ddescription)) . '</p>
                <p><strong>' . translate('Auteur') . '&nbsp;:</strong>&nbsp;' . $duser . '</p>
                <p><strong>' . translate('Page d\'accueil') . '&nbsp;:</strong>&nbsp;<a href="http://' . $dweb . '" target="_blank">' . $dweb . '</a></p>';

            if ($out_template == 1) {
                echo '
                    <a class="btn btn-primary" href="download.php?op=mydown&amp;did=' . $did . '" target="_blank" title="' . translate('Charger maintenant') . '" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="fa fa-lg fa-download"></i>
                    </a>
                    </div>
                </div>';
            }
        } else {
            Header('Location: download.php');
        }
    }


}