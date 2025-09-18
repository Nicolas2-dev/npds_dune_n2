<?php

namespace App\Library\Download;

use IntlDateFormatter;
use Npds\Config\Config;
use App\Support\Sanitize;
use App\Support\Facades\Auth;
use App\Support\Facades\Date;
use Npds\Support\Facades\View;
use App\Support\Facades\Language;
use App\Support\Facades\Paginator;
use App\Library\FileManagement\File;
use App\Library\Download\Traits\DownloadTrait;
use App\Library\FileManagement\FileManagement;


class Download
{

    use DownloadTrait;

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;


    /**
     * Retourne l'instance singleton du dispatcher.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }
    
    /**
     * Génère le bloc HTML pour les fichiers les plus téléchargés ou les derniers téléchargements.
     *
     * Cette fonction récupère les téléchargements depuis la base de données selon le critère
     * spécifié par `$ordre` (ex. 'dcounter' pour top downloads, 'ddate' pour derniers téléchargements),
     * tronque les noms de fichiers si nécessaire et applique les autorisations.
     *
     * @param string $form Type de formatage du rendu : 'short' ou autre (liste détaillée)
     * @param string $ordre Critère d'ordre : 'dcounter' ou 'ddate'
     * @return string HTML du bloc de téléchargement
     */
    public function topDownloadData(string $form, string $ordre): string
    {
        global $long_chain; // dans theme a revoir !

        if (!$long_chain) {
            $long_chain = 13;
        }

        $top = (int) Config::get('storie.top');

        $result = sql_query("SELECT did, dcounter, dfilename, dcategory, ddate, perms 
                            FROM " . sql_prefix('downloads') . " 
                            ORDER BY $ordre DESC 
                            LIMIT 0, $top");

        $lugar = 1;

        $ibid = '';

        while (list($did, $dcounter, $dfilename, $dcategory, $ddate, $dperm) = sql_fetch_row($result)) {
            if ($dcounter > 0) {
                $okfile = Auth::autorisation($dperm);

                if ($ordre == 'dcounter') {
                    $dd = Sanitize::wrh($dcounter);
                }

                if ($ordre == 'ddate') {
                    $dd = Date::formatTimes($ddate, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
                }

                $ori_dfilename = $dfilename;

                if (strlen($dfilename) > $long_chain) {
                    $dfilename = (substr($dfilename, 0, $long_chain)) . " ...";
                }

                if ($form == 'short') {
                    if ($okfile) {
                        $ibid .= '<li class="list-group-item list-group-item-action d-flex justify-content-start p-2 flex-wrap">
                            ' . $lugar . ' 
                            <a class="ms-2" href="download.php?op=geninfo&amp;did=' . $did . '&amp;out_template=1" title="' . $ori_dfilename . ' ' . $dd . '" data-bs-toggle="tooltip" >
                                ' . $dfilename . '
                            </a>
                            <span class="badge bg-secondary ms-auto align-self-center">' . $dd . '</span>
                        </li>';
                    }
                } else {
                    if ($okfile) {
                        $ibid .= '<li class="ms-4 my-1">
                            <a href="download.php?op=mydown&amp;did=' . $did . '" >
                                ' . $dfilename . '
                            </a> (' . translate('Catégorie') . ' : ' . Language::affLangue(stripslashes($dcategory)) . ')&nbsp;
                            <span class="badge bg-secondary float-end align-self-center">' . Sanitize::wrh($dcounter) . '</span>
                        </li>';
                    }
                }

                if ($okfile) {
                    $lugar++;
                }
            }
        }

        sql_free_result($result);

        return $ibid;
    }

    // Controller 

    public function list()
    {
        global $sortby, $dcategory;

        ob_start();

        if ($dcategory == '') {
            $dcategory = addslashes(Config::get('download.download_cat'));
        }

        $cate = stripslashes($dcategory);

        echo '<p class="lead">' . translate('Sélectionner une catégorie') . '</p>
        <div class="d-flex flex-column flex-sm-row flex-wrap justify-content-between my-3 border rounded">
            <p class="p-2 mb-0 ">';

        $acounter = sql_query("SELECT COUNT(*) 
                            FROM " . sql_prefix('downloads'));

        list($acount) = sql_fetch_row($acounter);

        if (($cate == translate('Tous')) or ($cate == '')) {
            echo '<i class="fa fa-folder-open fa-2x text-body-secondary align-middle me-2"></i><strong><span class="align-middle">' . translate('Tous') . '</span>
            <span class="badge bg-secondary ms-2 float-end my-2">' . $acount . '</span></strong>';
        } else {
            echo '<a href="download.php?dcategory=' . translate('Tous') . '&amp;sortby=' . $sortby . '"><i class="fa fa-folder fa-2x align-middle me-2"></i><span class="align-middle">' . translate('Tous') . '</span></a><span class="badge bg-secondary ms-2 float-end my-2">' . $acount . '</span>';
        }

        $result = sql_query("SELECT DISTINCT dcategory, COUNT(dcategory) 
                            FROM " . sql_prefix('downloads') . " 
                            GROUP BY dcategory 
                            ORDER BY dcategory");

        echo '</p>';

        while (list($category, $dcount) = sql_fetch_row($result)) {
            $category = stripslashes($category);

            echo '<p class="p-2 mb-0">';

            if ($category == $cate) {
                echo '<i class="fa fa-folder-open fa-2x text-body-secondary align-middle me-2"></i><strong class="align-middle">' . Language::affLangue($category) . '<span class="badge bg-secondary ms-2 float-end my-2">' . $dcount . '</span></strong>';
            } else {
                $category2 = urlencode($category);
                echo '<a href="download.php?dcategory=' . $category2 . '&amp;sortby=' . $sortby . '"><i class="fa fa-folder fa-2x align-middle me-2"></i><span class="align-middle">' . Language::affLangue($category) . '</span></a><span class="badge bg-secondary ms-2 my-2 float-end">' . $dcount . '</span>';
            }

            echo '</p>';
        }

        echo '</div>';

        $renderContent = ob_get_clean();

        View::share('download_list', $renderContent);
    }

    public function actDlTableHeader($dcategory, $sortby, $fieldname, $englishname)
    {
        //echo '<a class="d-none d-sm-inline" href="download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname . '" title="' . translate('Croissant') . '" data-bs-toggle="tooltip" ><i class="fa fa-sort-amount-down"></i></a>&nbsp;
        //' . translate($englishname) . '&nbsp;
        //<a class="d-none d-sm-inline" href="download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname . '&amp;sortorder=DESC" title="' . translate('Décroissant') . '" data-bs-toggle="tooltip" ><i class="fa fa-sort-amount-up"></i></a>';
    
        echo '<a class="d-none d-sm-inline" href="' . site_url('download/' . $dcategory . '/' . $fieldname) . '" title="' . translate('Croissant') . '" data-bs-toggle="tooltip" ><i class="fa fa-sort-amount-down"></i></a>&nbsp;
        ' . translate($englishname) . '&nbsp;
        <a class="d-none d-sm-inline" href="' . site_url('download/' . $dcategory . '/' . $fieldname . '/DESC') . '" title="' . translate('Décroissant') . '" data-bs-toggle="tooltip" ><i class="fa fa-sort-amount-up"></i></a>';
    
    }

    public function inactDlTableHeader($dcategory, $sortby, $fieldname, $englishname)
    {
        //echo '<a class="d-none d-sm-inline" href="download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname . '" title="' . translate('Croissant') . '" data-bs-toggle="tooltip"><i class="fa fa-sort-amount-down" ></i></a>&nbsp;
        //' . translate($englishname) . '&nbsp;
        //<a class="d-none d-sm-inline" href="download.php?dcategory=' . $dcategory . '&amp;sortby=' . $fieldname . '&amp;sortorder=DESC" title="' . translate('Décroissant') . '" data-bs-toggle="tooltip"><i class="fa fa-sort-amount-up" ></i></a>';
    
        echo '<a class="d-none d-sm-inline" href="' . site_url('download/' . $dcategory . '/' . $fieldname) . '" title="' . translate('Croissant') . '" data-bs-toggle="tooltip"><i class="fa fa-sort-amount-down" ></i></a>&nbsp;
        ' . translate($englishname) . '&nbsp;
        <a class="d-none d-sm-inline" href="' . site_url('download/' . $dcategory . '/' . $fieldname . '/DESC') . '" title="' . translate('Décroissant') . '" data-bs-toggle="tooltip"><i class="fa fa-sort-amount-up" ></i></a>';
    }

    public function dlTableHeader()
    {
        echo '</td>
        <td>';
    }

    public function popUploader($did, $ddescription, $dcounter, $dfilename, $aff)
    {
        $out_template = 0;

        if ($aff) {
            echo '<a class="me-3" href="#" data-bs-toggle="modal" data-bs-target="#mo' . $did . '" title="' . translate('Information sur le fichier') . '" data-bs-toggle="tooltip"><i class="fa fa-info-circle fa-2x"></i></a>
                <a href="download.php?op=mydown&amp;did=' . $did . '" target="_blank" title="' . translate('Charger maintenant') . '" data-bs-toggle="tooltip"><i class="fa fa-download fa-2x"></i></a>
                <div class="modal fade" id="mo' . $did . '" tabindex="-1" role="dialog" aria-labelledby="my' . $did . '" aria-hidden="true">
                    <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h4 class="modal-title text-start" id="my' . $did . '">' . translate('Information sur le fichier') . ' - ' . $dfilename . '</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" title=""></button>
                        </div>
                        <div class="modal-body text-start">';

            $this->genInfo($did, $out_template);

            echo '</div>
                        <div class="modal-footer">
                            <a class="" href="download.php?op=mydown&amp;did=' . $did . '" title="' . translate('Charger maintenant') . '"><i class="fa fa-2x fa-download"></i></a>
                        </div>
                    </div>
                </div>
            </div>';
        }
    }

    public function sortLinks($dcategory, $sortby)
    {
        global $user;

        $dcategory = stripslashes($dcategory);

        echo '<thead>
            <tr>
                <th class="text-center">' . translate('Fonctions') . '</th>
                <th class="text-center">';
                
        if ($sortby == 'dfiletype' or !$sortby) {
            $this->actDlTableHeader($dcategory, $sortby, 'dfiletype', 'Type');
        } else {
            $this->inactDlTableHeader($dcategory, $sortby, 'dfiletype', 'Type');
        }

        echo '</th>
        <th class="text-center">';

        if ($sortby == 'dfilename' or !$sortby) {
            $this->actDlTableHeader($dcategory, $sortby, 'dfilename', 'Nom');
        } else {
            $this->inactDlTableHeader($dcategory, $sortby, 'dfilename', 'Nom');
        }

        echo '</th>
        <th class="text-center">';

        if ($sortby == "dfilesize") {
            $this->actDlTableHeader($dcategory, $sortby, 'dfilesize', 'Taille');
        } else {
            $this->inactDlTableHeader($dcategory, $sortby, 'dfilesize', 'Taille');
        }

        echo '</th>
        <th class="text-center">';

        if ($sortby == "dcategory") {
            $this->actDlTableHeader($dcategory, $sortby, 'dcategory', 'Catégorie');
        } else {
            $this->inactDlTableHeader($dcategory, $sortby, 'dcategory', 'Catégorie');
        }

        echo '</th>
        <th class="text-center">';

        if ($sortby == "ddate") {
            $this->actDlTableHeader($dcategory, $sortby, 'ddate', 'Date');
        } else {
            $this->inactDlTableHeader($dcategory, $sortby, 'ddate', 'Date');
        }

        echo '</th>
        <th class="text-center">';

        if ($sortby == "dver") {
            $this->actDlTableHeader($dcategory, $sortby, 'dver', 'Version');
        } else {
            $this->inactDlTableHeader($dcategory, $sortby, 'dver', 'Version');
        }

        echo '</th>
        <th class="text-center">';

        if ($sortby == "dcounter") {
            $this->actDlTableHeader($dcategory, $sortby, 'dcounter', 'Compteur');
        } else {
            $this->inactDlTableHeader($dcategory, $sortby, 'dcounter', 'Compteur');
        }

        echo '</th>';

        if ($user or Auth::autorisation(-127)) {
            echo '<th class="text-center n-t-col-xs-1"></th>';
        }

        echo '</tr>
        </thead>';
    }

    public function listDownloads($dcategory, $sortby, $sortorder)
    {
        global $page, $user;

        ob_start();

        if ($dcategory == '') {
            $dcategory = addslashes(Config::get('download.download_cat'));
        }

        if (!$sortby) {
            $sortby = 'dfilename';
        }

        if (($sortorder != "ASC") && ($sortorder != "DESC")) {
            $sortorder = "ASC";
        }

        echo '<p class="lead">';

        echo translate('Affichage filtré pour') . "&nbsp;<i>";

        if ($dcategory == translate('Tous')) {
            echo '<b>' . translate('Tous') . '</b>';
        } else {
            echo '<b>' . Language::affLangue(stripslashes($dcategory)) . '</b>';
        }

        echo '</i>&nbsp;' . translate('trié par ordre') . '&nbsp;';

        // Shiney SQL Injection 11/2011
        $sortby2 = '';

        if ($sortby == 'dfilename') {
            $sortby2 = translate('Nom') . "";
        }

        if ($sortby == 'dfilesize') {
            $sortby2 = translate('Taille du fichier') . "";
        }

        if ($sortby == 'dcategory') {
            $sortby2 = translate('Catégorie') . "";
        }

        if ($sortby == 'ddate') {
            $sortby2 = translate('Date de création') . "";
        }

        if ($sortby == 'dver') {
            $sortby2 = translate('Version') . "";
        }

        if ($sortby == 'dcounter') {
            $sortby2 = translate('Chargements') . "";
        }

        // Shiney SQL Injection 11/2011
        if ($sortby2 == '') {
            $sortby = 'dfilename';
        }

        echo translate('de') . '&nbsp;<i><b>' . $sortby2 . '</b></i>
        </p>';

        echo '<table class="table table-hover mb-3 table-sm" id ="lst_downlo" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-show-columns="true"
        data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons-prefix="fa" data-icons="icons">';

        $this->sortLinks($dcategory, $sortby);

        echo '<tbody>';

        if ($dcategory == translate('Tous')) {
            $sql = "SELECT COUNT(*) 
                    FROM " . sql_prefix('downloads');
        } else {
            $sql = "SELECT COUNT(*) 
                    FROM " . sql_prefix('downloads') . " 
                    WHERE dcategory='" . addslashes($dcategory) . "'";
        }

        $result = sql_query($sql);
        list($total) =  sql_fetch_row($result);

        $perpage = Config::get('download.perpage');

        //
        if ($total > $perpage) {
            $pages = ceil($total / $perpage);

            if ($page > $pages) {
                $page = $pages;
            }

            if (!$page) {
                $page = 1;
            }

            $offset = ($page - 1) * $perpage;
        } else {
            $offset = 0;
            $pages = 1;
            $page = 1;
        }

        //  
        $nbPages = ceil($total / $perpage);
        $current = 1;

        if ($page >= 1) {
            $current = $page;
        } elseif ($page < 1) {
            $current = 1;
        } else {
            $current = $nbPages;
        }

        if ($dcategory == translate('Tous')) {
            $sql = "SELECT * FROM " . sql_prefix('downloads') . " 
                    ORDER BY $sortby $sortorder 
                    LIMIT $offset,$perpage";
        } else {
            $sql = "SELECT * FROM " . sql_prefix('downloads') . " 
                    WHERE dcategory='" . addslashes($dcategory) . "' 
                    ORDER BY $sortby $sortorder 
                    LIMIT $offset,$perpage";
        }

        $result = sql_query($sql);

        while (list($did, $dcounter, $durl, $dfilename, $dfilesize, $ddate, $dweb, $duser, $dver, $dcat, $ddescription, $dperm) = sql_fetch_row($result)) {

            $Fichier = new File($durl); // keep for extension
            $FichX = new FileManagement;
            $okfile = '';

            if (!stristr($dperm, ',')) {
                $okfile = Auth::autorisation($dperm);
            } else {
                $ibidperm = explode(',', $dperm);

                foreach ($ibidperm as $v) {
                    if (Auth::autorisation($v) == true) {
                        $okfile = true;
                        break;
                    }
                }
            }

            echo '<tr>
            <td class="text-center">';

            if ($okfile == true) {
                echo $this->popUploader($did, $ddescription, $dcounter, $dfilename, true);
            } else {
                echo $this->popUploader($did, $ddescription, $dcounter, $dfilename, false);
                echo '<span class="text-danger"><i class="fa fa-ban fa-lg me-1"></i>' . translate('Privé') . '</span>';
            }

            echo '</td>
            <td class="text-center">' . $Fichier->afficheExtention('webfont') . '</td>
            <td>';

            if ($okfile == true) {
                echo '<a href="download.php?op=mydown&amp;did=' . $did . '" target="_blank">' . $dfilename . '</a>';
            } else {
                echo '<span class="text-danger"><i class="fa fa-ban fa-lg me-1"></i>...</span>';
            }

            echo '</td>
            <td class="small text-center">';

            echo ($dfilesize != 0)
                ? $FichX->fileSizeFormat($dfilesize, 1)
                : $FichX->fileSizeAuto($durl, 2);

            echo '</td>
                <td>' . Language::affLangue(stripslashes($dcat)) . '</td>
                <td class="small text-center">' . Date::formatTimes($ddate, IntlDateFormatter::SHORT, IntlDateFormatter::NONE) . '</td>
                <td class="small text-center">' . $dver . '</td>
                <td class="small text-center">' . Sanitize::wrh($dcounter) . '</td>';

            if ($user != '' or Auth::autorisation(-127)) {
                echo '<td>';

                if (($okfile == true and $user != '') or Auth::autorisation(-127)) {
                    echo '<a href="download.php?op=broken&amp;did=' . $did . '" title="' . translate('Rapporter un lien rompu') . '" data-bs-toggle="tooltip"><i class="fas fa-lg fa-unlink"></i></a>';
                }

                echo '</td>';
            }

            echo '</tr>';
        }

        echo '</tbody>
        </table>';

        $dcategory = StripSlashes($dcategory);

        echo '<div class="mt-3"></div>
        ' . Paginator::paginateSingle('download.php?dcategory=' . $dcategory . '&amp;sortby=' . $sortby . '&amp;sortorder=' . $sortorder . '&amp;page=', '', $nbPages, $current, $adj = 3, 0, $page);
    
    
        $renderContent = ob_get_clean();

        View::share('download_lists', $renderContent);
    }
    
    /**
     * Nettoie une catégorie en appliquant urldecode + htmlspecialchars + stripslashes.
     *
     * @param  string|null  $value
     * @return string
     */
    public function sanitizeCategory(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return stripslashes(
            htmlspecialchars(
                urldecode($value),
                ENT_QUOTES,
                'UTF-8'
            )
        );
    }


}
