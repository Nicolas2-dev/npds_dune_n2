<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\BaseController;


class AdminBaseController extends BaseController 
{

    /**
     * Method executed before any action.
     */
    protected function initialize()
    {
        /*
        #autodoc $admintest - $super_admintest : permet de savoir si un admin est connect&ecute; ($admintest=true) et s'il est SuperAdmin ($super_admintest=true)
        $admintest = false;
        $super_admintest = false;

        if (isset($admin) and ($admin != '')) {
            $Xadmin = base64_decode($admin);
            $Xadmin = explode(':', $Xadmin);

            $aid = urlencode($Xadmin[0]);

            $AIpwd = $Xadmin[1];

            if ($aid == '' or $AIpwd == '') {
                Admin_Alert('Null Aid or Passwd');
            }

            $result = sql_query("SELECT pwd, radminsuper 
                                FROM " . sql_prefix('authors') . " 
                                WHERE aid = '$aid'");

            if (!$result) {
                Admin_Alert(sprintf('DB not ready #2 : %s / %s', $aid, $AIpwd));
            } else {
                list($AIpass, $Xsuper_admintest) = sql_fetch_row($result);

                if (md5($AIpass) == $AIpwd and $AIpass != '') {
                    $admintest = true;
                    $super_admintest = $Xsuper_admintest;
                } else {
                    Admin_Alert(printf('Password in Cookies not Good #1 : %s / %s', $aid, $AIpwd));
                }
            }

            unset($AIpass);
            unset($AIpwd);
            unset($Xadmin);
            unset($Xsuper_admintest);
        }  

        // case :
        // tout ce bordel va être deprecated !
        if ($admintest) {

            settype($op, 'string');

            switch ($op) {
                case 'GraphicAdmin':
                    GraphicAdmin($hlpfile);
                    break;

                // FILES MANAGER
                case 'FileManager':
                    if ($admintest and $filemanager) {
                        header('location: modules.php?ModPath=f-manager&ModStart=f-manager&FmaRep=' . $aid);
                    }
                    break;

                case 'FileManagerDisplay':
                    if ($admintest and $filemanager) {
                        header('location: modules.php?ModPath=f-manager&ModStart=f-manager&FmaRep=download');
                    }
                    break;

            }
        } 
        */  
        
        parent::initialize();
    }

    // contoller ou lib Auth ?
    public function admindroits($aid, $f_meta_nom)
    {
        global $radminsuper;

        $res = sql_query("SELECT fnom, radminsuper 
                        FROM " . sql_prefix('authors') . " a 
                        LEFT JOIN " . sql_prefix('droits') . " d ON a.aid = d.d_aut_aid 
                        LEFT JOIN " . sql_prefix('fonctions') . " f ON d.d_fon_fid = f.fdroits1 
                        WHERE a.aid='$aid'");

        $foncts = [];
        $supers = [];

        while ($data = sql_fetch_row($res)) {
            $foncts[] = $data[0];
            $supers[] = $data[1];
        }

        if ((!in_array('1', $supers)) and (!in_array($f_meta_nom, $foncts))) {
            Access_Error();
        }

        $radminsuper = $supers[0];
    }

    // controller ou lib Graphic ?
    public function adminhead($f_meta_nom, $f_titre, $adminimg)
    {
        global $admf_ext, $ModPath, $adm_img_mod;

        $query = sql_query("SELECT furlscript, ficone 
                            FROM " . sql_prefix('fonctions') . " 
                            WHERE fnom='$f_meta_nom'");

        list($furlscript, $ficone) = sql_fetch_row($query);

        if (file_exists($adminimg . $ficone . '.' . $admf_ext)) {
            $img_adm = '<img src="' . $adminimg . $ficone . '.' . $admf_ext . '" class="me-2" alt="' . $f_titre . '" loading="lazy" />';
        } elseif (stristr($_SERVER['QUERY_STRING'], "Extend-Admin-SubModule") || $adm_img_mod == 1) {

            $img_adm = (file_exists('modules/' . $ModPath . '/assets/images/' . $ModPath . '.' . $admf_ext))
                ? '<img src="modules/' . $ModPath . '/assets/images/' . $ModPath . '.' . $admf_ext . '" class="me-2" alt="' . $f_titre . '" loading="lazy" />'
                : '';
        } else {
            $img_adm = '';
        }

        $entete_adm = '<div id="adm_workarea" class="adm_workarea">
            <h2><a ' . $furlscript . ' >' . $img_adm . $f_titre . '</a></h2>';

        echo $entete_adm;
    }

    // controller ou lib Graphic ?
    // public function trop volumineuse, retirer les alerte en faire une lib, idem pour la messagerie, idem helpfile
    public function GraphicAdmin($hlpfile)
    {
        global $aid, $admingraphic, $adminimg, $language, $admin, $banners, $filemanager, $Version_Sub, $Version_Num, $httprefmax, $httpref, $short_menu_admin, $admf_ext, $adm_ent, $nuke_url, $Default_Theme;

        $bloc_foncts = '';
        $bloc_foncts_A = '';

        $Q = sql_fetch_assoc(sql_query("SELECT * 
                                        FROM " . sql_prefix('authors') . " 
                                        WHERE aid='$aid' 
                                        LIMIT 1"));

        // recupérations des états des fonctions d'ALERTE ou activable et maj (faire une fonction avec cache court dev ..)

        //article à valider
        $newsubs = sql_num_rows(sql_query("SELECT qid 
                                        FROM " . sql_prefix('queue')));

        if ($newsubs) {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1', fretour='" . $newsubs . "',fretour_h='" . adm_translate('Articles en attente de validation !') . "' 
                    WHERE fid='38'");
        } else {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='0', fretour='0' 
                    WHERE fid='38'");
        }

        //news auto
        $newauto = sql_num_rows(sql_query("SELECT anid 
                                        FROM " . sql_prefix('autonews')));

        if ($newauto) {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1', fretour='" . $newauto . "',fretour_h='" . adm_translate('Articles programmés pour la publication.') . "' 
                    WHERE fid=37");
        } else {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='0', fretour='0',fretour_h='' 
                    WHERE fid=37");
        }

        //etat filemanager
        if ($filemanager) {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1' 
                    WHERE fid='27'");
        } else {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='0' 
                    WHERE fid='27'");
        }

        // récuperation traitement des messages de NPDS
        $QM = sql_query("SELECT * FROM " . sql_prefix('fonctions') . " 
                        WHERE fnom REGEXP'mes_npds_[[:digit:]]'");

        settype($f_mes, 'array');

        while ($SQM = sql_fetch_assoc($QM)) {
            $f_mes[] = $SQM['fretour_h'];
        }

        // récuperation
        $messagerie_npds = file_get_contents('https://raw.githubusercontent.com/npds/npds_dune/master/versus.txt');
        $messages_npds = explode("\n", $messagerie_npds);

        array_pop($messages_npds);

        // traitement spécifique car message de version permanent "Versus"
        $versus_info = explode('|', $messages_npds[0]);

        if ($versus_info[1] == $Version_Sub and $versus_info[2] == $Version_Num) {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1', fretour='', fretour_h='" . adm_translate('Version NPDS') . " " . $Version_Sub . " " . $Version_Num . "', furlscript='' 
                    WHERE fid='36'");
        } else {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1', fretour='N', furlscript='data-bs-toggle=\"modal\" data-bs-target=\"#versusModal\"', fretour_h='" . adm_translate('Une nouvelle version NPDS est disponible !') . "<br />" . $versus_info[1] . " " . $versus_info[2] . "<br />" . adm_translate('Cliquez pour télécharger.') . "' 
                    WHERE fid='36'");
        }

        $mess = array_slice($messages_npds, 1); //on enleve le message de version

        //si pas de message on nettoie la table
        if (empty($mess)) {
            sql_query("DELETE FROM " . sql_prefix('fonctions') . " 
                    WHERE fnom REGEXP'mes_npds_[[:digit:]]'");

            sql_query("ALTER TABLE " . sql_prefix('fonctions') . " 
                    AUTO_INCREMENT = (SELECT MAX(fid)+1 FROM " . sql_prefix('fonctions') . ")");
        } else {
            $o = 0;

            foreach ($mess as $v) {
                $ibid = explode('|', $v);
                $fico = $ibid[0] != 'Note' ? 'message_npds_a' : 'message_npds_i';

                $QM = sql_num_rows(sql_query("SELECT * 
                                            FROM " . sql_prefix('fonctions') . " 
                                            WHERE fnom='mes_npds_" . $o . "'"));

                if ($QM == 0) {
                    sql_query("INSERT INTO " . sql_prefix('fonctions') . " (fnom, fretour_h, fcategorie, fcategorie_nom, ficone, fetat, finterface, fnom_affich, furlscript) 
                            VALUES ('mes_npds_" . $o . "', '" . addslashes($ibid[1]) . "', '9','Alerte', '" . $fico . "', '1', '1', '" . addslashes($ibid[2]) . "', 'data-bs-toggle=\"modal\" data-bs-target=\"#messageModal\"');\n");
                }

                $o++;
            }
        }

        // si message on compare avec la base
        if ($mess) {
            for ($i = 0; $i < count($mess); $i++) {

                $ibid = explode('|', $mess[$i]);
                $fico = $ibid[0] != 'Note' ? 'message_npds_a' : 'message_npds_i';

                //si on trouve le contenu du fichier dans la requete
                if (in_array($ibid[1], $f_mes, true)) {
                    $k = (array_search($ibid[1], $f_mes));

                    unset($f_mes[$k]);

                    $result = sql_query("SELECT fnom_affich 
                                        FROM " . sql_prefix('fonctions') . " 
                                        WHERE fnom='mes_npds_$i'");

                    if (sql_num_rows($result) == 1) {
                        $alertinfo = sql_fetch_assoc($result);

                        if ($alertinfo['fnom_affich'] != $ibid[2]) {
                            sql_query('UPDATE ' . sql_prefix('fonctions') . ' 
                                    SET fdroits1_descr="", fnom_affich="' . addslashes($ibid[2]) . '" 
                                    WHERE fnom="mes_npds_' . $i . '"');
                        }
                    }
                }
            }

            if (count($f_mes) !== 0) {
                foreach ($f_mes as $v) {
                    sql_query('DELETE from ' . sql_prefix('fonctions') . ' 
                            WHERE fretour_h="' . $v . '" 
                            AND fcategorie="9"');
                }
            }
        }
        // recuperation traitement des messages de NPDS

        // utilisateur à valider
        $newsuti = sql_num_rows(sql_query("SELECT uid FROM " . sql_prefix('users_status') . " 
                                        WHERE uid!='1' 
                                        AND open='0'"));
        if ($newsuti) {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1', fretour='" . $newsuti . "', fretour_h='" . adm_translate('Utilisateur en attente de validation !') . "' 
                    WHERE fid='44'");
        } else {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='0', fretour='0' 
                    WHERE fid='44'");
        }

        // référants à gérer
        if ($httpref == 1) {
            $result = sql_fetch_assoc(sql_query("SELECT COUNT(*) AS total 
                                                FROM " . sql_prefix('referer')));

            if ($result['total'] >= $httprefmax) {
                sql_query("UPDATE " . sql_prefix('fonctions') . " 
                        SET fetat='1', fretour='!!!' 
                        WHERE fid='39'");
            } else {
                sql_query("UPDATE " . sql_prefix('fonctions') . " 
                        SET fetat='0' 
                        WHERE fid='39'");
            }
        }

        // critique en attente
        $critsubs = sql_num_rows(sql_query("SELECT * 
                                            FROM " . sql_prefix('reviews_add')));

        if ($critsubs) {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1', fretour='" . $critsubs . "', fretour_h='" . adm_translate('Critique en attente de validation.') . "' 
                    WHERE fid='35'");
        } else {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='0', fretour='0' 
                    WHERE fid='35'");
        }

        // nouveau lien à valider
        $newlink = sql_num_rows(sql_query("SELECT * 
                                        FROM " . sql_prefix('links_newlink')));

        if ($newlink) {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1', fretour='" . $newlink . "', fretour_h='" . adm_translate('Liens à valider.') . "' 
                    WHERE fid='41'");
        } else {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='0', fretour='0' 
                    WHERE fid='41'");
        }

        // lien rompu à valider
        $brokenlink = sql_num_rows(sql_query("SELECT * 
                                            FROM " . sql_prefix('links_modrequest') . " where brokenlink='1'"));

        if ($brokenlink) {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1', fretour='" . $brokenlink . "', fretour_h='" . adm_translate('Liens rompus à valider.') . "' 
                    WHERE fid='42'");
        } else {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='0', fretour='0' 
                    WHERE fid='42'");
        }

        // nouvelle publication
        $newpubli = $Q['radminsuper'] == 1
            ? sql_num_rows(sql_query("SELECT * 
                                    FROM " . sql_prefix('seccont_tempo')))

            : sql_num_rows(sql_query("SELECT * 
                                    FROM " . sql_prefix('seccont_tempo') . " 
                                    WHERE author='$aid'"));

        if ($newpubli) {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1', fretour='" . $newpubli . "', fretour_h='" . adm_translate('Publication(s) en attente de validation') . "' 
                    WHERE fid='50'");
        } else {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='0', fretour='0' 
                    WHERE fid='50'");
        }

        // utilisateur(s) en attente de groupe
        $directory = 'storage/users_private/groupe';
        $iterator = new DirectoryIterator($directory);

        $j = 0;

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() and strpos($fileinfo->getFilename(), 'ask4group') !== false) {
                $j++;
            }
        }

        if ($j > 0) {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='1', fretour='" . $j . "', fretour_h='" . adm_translate('Utilisateur en attente de groupe !') . "' 
                    WHERE fid='46'");
        } else {
            sql_query("UPDATE " . sql_prefix('fonctions') . " 
                    SET fetat='0', fretour='0' 
                    WHERE fid='46'");
        }
        // etc...etc recupérations des états des fonctions d'ALERTE et maj

        // Pour les modules installés produisant des notifications
        $alert_modules = sql_query("SELECT * FROM " . sql_prefix('fonctions') . " f 
                                    LEFT JOIN " . sql_prefix('modules') . " m ON m.mnom = f.fnom 
                                    WHERE m.minstall=1 
                                    AND fcategorie=9");

        if ($alert_modules) {
            while ($am = sql_fetch_array($alert_modules)) {

                include 'modules/' . $am['fnom'] . '/admin/adm_alertes.php';

                $nr = count($reqalertes);

                $i = 0;
                while ($i < $nr) {
                    $ibid = sql_num_rows(sql_query($reqalertes[$i][0]));

                    if ($ibid) {
                        $fr = $reqalertes[$i][1] != 1 ? $reqalertes[$i][1] : $ibid;

                        sql_query("UPDATE " . sql_prefix('fonctions') . " 
                                SET fetat='1', fretour='" . $fr . "', fretour_h='" . $reqalertes[$i][2] . "' 
                                WHERE fid=" . $am['fid'] . "");
                    } else {
                        sql_query("UPDATE " . sql_prefix('fonctions') . " 
                                SET fetat='0', fretour='' 
                                WHERE fid=" . $am['fid'] . "");
                    }
                    $i++;
                }
            }
        }
        // Pour les modules installés produisant des notifications

        // construction des blocs menu : selection de fonctions actives ayant une interface graphique de premier niveau et dont l'administrateur connecté en posséde les droits d'accès

        // on prend tout ce qui a une interface 
        $R = $Q['radminsuper'] == 1
            ? sql_query("SELECT * FROM " . sql_prefix('fonctions') . " f 
                        WHERE f.finterface =1 
                        AND f.fetat != '0' 
                        ORDER BY f.fcategorie, f.fordre")

            : sql_query("SELECT * FROM " . sql_prefix('fonctions') . " f 
                        LEFT JOIN " . sql_prefix('droits') . " d ON f.fdroits1 = d.d_fon_fid 
                        LEFT JOIN " . sql_prefix('authors') . " a ON d.d_aut_aid =a.aid 
                        WHERE f.finterface=1 
                        AND fetat!=0 
                        AND d.d_aut_aid='$aid' 
                        AND d.d_droits REGEXP'^1' 
                        ORDER BY f.fcategorie, f.fordre");

        $j = 0;

        while ($SAQ = sql_fetch_assoc($R)) {

            $cat[]      = $SAQ['fcategorie'];
            $cat_n[]    = $SAQ['fcategorie_nom'];
            $fid_ar[]   = $SAQ['fid'];

            $adm_lecture = explode('|', $SAQ['fdroits1_descr'] ?? '');

            if ($SAQ['fcategorie'] == 6 or ($SAQ['fcategorie'] == 9 and strstr($SAQ['furlscript'], 'op=Extend-Admin-SubModule'))) {
                if (file_exists('modules/' . $SAQ['fnom'] . '/assets/images/' . $SAQ['ficone'] . '.' . $admf_ext)) {
                    $adminico = 'modules/' . $SAQ['fnom'] . '/assets/images/' . $SAQ['ficone'] . '.' . $admf_ext;
                } else {
                    $adminico = $adminimg . 'module.' . $admf_ext;
                }
            } else {
                $adminico = $adminimg . $SAQ['ficone'] . '.' . $admf_ext;
            }

            if ($SAQ['fcategorie'] == 9) {
                if (preg_match('#mes_npds_\d#', $SAQ['fnom'])) {
                    if (!in_array($aid, $adm_lecture, true))
                        $bloc_foncts_A .= '
                        <a class=" btn btn-outline-primary btn-sm me-2 my-1 tooltipbyclass" title="' . $SAQ['fretour_h'] . '" data-id="' . $SAQ['fid'] . '" data-bs-html="true" ' . $SAQ['furlscript'] . ' >
                            <img class="adm_img" src="' . $adminico . '" alt="icon_message" loading="lazy" />
                            <span class="badge bg-danger ms-1">' . $SAQ['fretour'] . '</span>
                        </a>';
                } else {
                    $bloc_foncts_A .= '
                    <a class=" btn btn-outline-primary btn-sm me-2 my-1 tooltipbyclass" title="' . $SAQ['fretour_h'] . '" data-id="' . $SAQ['fid'] . '" data-bs-html="true" ' . $SAQ['furlscript'] . ' >
                        <img class="adm_img" src="' . $adminico . '" alt="icon_message" loading="lazy" />
                        <span class="badge rounded-pill bg-danger ms-1">' . $SAQ['fretour'] . '</span>
                    </a>';
                }

                array_pop($cat_n);
            } else {
                $ul_o = '
                <h4 class="text-body-secondary"><a class="tog" id="hide_' . strtolower(substr($SAQ['fcategorie_nom'], 0, 3)) . '" title="' . adm_translate('Replier la liste') . '" style="clear:left;"><i id="i_' . strtolower(substr($SAQ['fcategorie_nom'], 0, 3)) . '" class="fa fa-caret-up fa-lg text-primary" ></i></a>&nbsp;' . adm_translate($SAQ['fcategorie_nom']) . '</h4>
                <ul id="' . strtolower(substr($SAQ['fcategorie_nom'], 0, 3)) . '" class="list" style="clear:left;">';

                $li_c = '
                <li id="' . $SAQ['fid'] . '"  data-bs-toggle="tooltip" data-bs-placement="top" title="';

                $li_c .= $SAQ['fcategorie'] == 6 ? $SAQ['fnom_affich'] : adm_translate($SAQ['fnom_affich']);

                // lancement du FileManager 
                $blank = '';

                if ($SAQ['fnom'] == "FileManager") {
                    if (file_exists($path = 'modules/f-manager/storage/users/' . strtolower($aid) . '.php')) {

                        include $path;

                        if (!$NPDS_fma) {
                            $blank = ' target="_blank"';
                        }
                    }
                }

                $li_c .= '"><a class="btn btn-outline-primary" ' . $SAQ['furlscript'] . $blank . '>';

                if ($admingraphic == 1) {
                    $li_c .= '<img class="adm_img" src="' . $adminico . '" alt="icon_' . $SAQ['fnom_affich'] . '" loading="lazy" />';
                } else {
                    $li_c .= $SAQ['fcategorie'] == 6 ? $SAQ['fnom_affich'] : adm_translate($SAQ['fnom_affich']);
                }

                $li_c .= '</a></li>';
                $ul_f = '';

                if ($j !== 0) {
                    $ul_f = '
                    </ul>
                    <script type="text/javascript">
                        //<![CDATA[
                            $( document ).ready(function() {
                                tog(\'' . strtolower(substr($cat_n[($j - 1)], 0, 3)) . '\',\'show_' . strtolower(substr($cat_n[$j - 1], 0, 3)) . '\',\'hide_' . strtolower(substr($cat_n[$j - 1], 0, 3)) . '\');
                            })
                        //]]>
                    </script>';
                }

                if ($j == 0) {
                    $bloc_foncts .= $ul_o . $li_c;
                } else {
                    $bloc_foncts .= ($j > 0 and $cat[$j] > $cat[$j - 1]) ? $ul_f . $ul_o . $li_c : $li_c;
                }
            }
            $j++;
        }

        if (isset($cat_n)) {

            $ca = array();
            $ca = array_unique($cat_n);
            $ca = array_pop($ca);

            $bloc_foncts .= '
            </ul>
            <script type="text/javascript">
                //<![CDATA[
                    $( document ).ready(function() {
                        tog(\'' . strtolower(substr($ca, 0, 3)) . '\',\'show_' . strtolower(substr($ca, 0, 3)) . '\',\'hide_' . strtolower(substr($ca, 0, 3)) . '\');
                    })
                //]]>
            </script>';
        }

        echo "<script type=\"text/javascript\">
            //<![CDATA[
                function openwindow(){
                    window.open (\"$hlpfile\",\"Help\",\"toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=400\");
                }
                $(function () {
                    $('[data-bs-toggle=\"tooltip\"]').tooltip();
                    $('[data-bs-toggle=\"popover\"]').popover();
                    $('table').on('all.bs.table', function (e, name, args) {
                        $('[data-bs-toggle=\"tooltip\"]').tooltip();
                        $('[data-bs-toggle=\"popover\"]').popover();
                    });
                });
                // date d'expiration connection admin
                $(function () {
                    var dae = Cookies.get('adm_exp')*1000,
                        dajs = new Date(dae);
                    $('#adm_connect_status').attr('title', 'Connexion ouverte jusqu\'au : '+dajs.getDate()+'/'+ (dajs.getMonth()+1) +'/'+ dajs.getFullYear() +'/'+ dajs.getHours() +':'+ dajs.getMinutes()+':'+ dajs.getSeconds()+' GMT')
                    deCompte= function() {
                        var date1 = new Date(), sec = (dae - date1) / 1000, n = 24 * 3600;
                        if (sec > 0) {
                            j = Math.floor (sec / n);
                            h = Math.floor ((sec - (j * n)) / 3600);
                            mn = Math.floor ((sec - ((j * n + h * 3600))) / 60);
                            sec = Math.floor (sec - ((j * n + h * 3600 + mn * 60)));
                            $('#tempsconnection').text(j +'j '+ h +':'+ mn +':'+sec);
                        }
                        t_deCompte=setTimeout (deCompte, 1000);
                    }
                    deCompte();
                })
                // date d'expiration connection admin
                tog = function(lst,sho,hid) {
                    $('#adm_men, #adm_workarea').on('click', 'a.tog', function() {
                        var buttonID = $(this).attr('id');
                        lst_id = $('#'+lst);
                        i_id=$('#i_'+lst);
                        btn_show=$('#'+sho);
                        btn_hide=$('#'+hid);
                        if (buttonID == sho) {
                            lst_id.fadeIn(1300);//show();
                            btn_show.attr('id',hid)
                            btn_show.attr('title','" . html_entity_decode(adm_translate('Replier la liste'), ENT_QUOTES | ENT_HTML401, 'UTF-8') . "');
                            i_id.attr('class','fa fa-caret-up fa-lg text-primary me-1');
                        } else if (buttonID == hid) {
                            lst_id.fadeOut(1300);//hide();
                            btn_hide=$('#'+hid);
                            btn_hide.attr('id',sho);
                            btn_hide.attr('title','" . html_entity_decode(adm_translate('Déplier la liste'), ENT_QUOTES | ENT_HTML401, 'UTF-8') . "');
                            i_id.attr('class','fa fa-caret-down fa-lg text-primary me-1');
                        }
                    });
                };
                $(function () {
                    $('#messageModal').on('show.bs.modal', function (event) {
                        var button = $(event.relatedTarget); 
                        var id = button.data('id');
                        $('#messageModalId').val(id);
                        $('#messageModalForm').attr('action', '" . $nuke_url . "/admin.php?op=alerte_update');
                        $.ajax({
                            url:\"" . $nuke_url . "/admin.php?op=alerte_api\",
                            method: \"POST\",
                            data:{id:id},
                            dataType:\"JSON\",
                            success:function(data) {
                                var fnom_affich = JSON.stringify(data['fnom_affich']),
                                    fretour_h = JSON.stringify(data['fretour_h']),
                                    ficone = JSON.stringify(data['ficone']);
                                $('#messageModalLabel').html(JSON.parse(fretour_h));
                                $('#messageModalContent').html(JSON.parse(fnom_affich));
                                $('#messageModalIcon').html('<img src=\"assets/images/admin/'+JSON.parse(ficone)+'.png\" />');
                            }
                        });
                    });
                });
            //]]>
        </script>\n";

        $adm_ent = '';

        $adm_ent .= '<h1 class="mb-3">' . adm_translate('Administration') . '</h1>
        <div id ="adm_men" class="mb-4">
            <div id="adm_header" class="row justify-content-end">
                <div class="col-6 col-lg-6 men_tit align-self-center">
                    <h2><a href="admin.php">' . adm_translate('Menu') . '</a></h2>
                </div>
                <div id="adm_men_man" class="col-6 col-lg-6 men_man text-end">
                    <ul class="liste" id="lst_men_top">
                        <li data-bs-toggle="tooltip" title="' . adm_translate('Déconnexion') . '" ><a class="btn btn-outline-danger btn-sm" href="admin.php?op=logout" ><i class="fas fa-sign-out-alt fa-2x"></i></a></li>';

        if ($hlpfile) {
            $adm_ent .= '<li class="ms-2" data-bs-toggle="tooltip" title="' . adm_translate('Manuel en ligne') . '">
                <a class="btn btn-outline-primary btn-sm" href="javascript:openwindow();">
                    <i class="fa fa-question-circle fa-2x"></i>
                </a>
            </li>';
        }

        $adm_ent .= '</ul>
            </div>
        </div>
        <div id="adm_men_dial" class="border rounded px-2 py-2" >
            <div id="adm_men_alert" >
                <div id="alertes">
                    ' . Language::affLangue($bloc_foncts_A) . '
                </div>
            </div>
        </div>
        <div id ="mes_perm" class="contenair-fluid text-body-secondary" >
            <span class="car">' . $Version_Sub . ' ' . $Version_Num . ' ' . $aid . ' </span><span id="tempsconnection" class="car"></span>
        </div>
        <div class="modal fade" id="versusModal" tabindex="-1" aria-labelledby="versusModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="versusModalLabel"><img class="adm_img me-2" src="assets/images/admin/message_npds.png" alt="icon_" />' . adm_translate('Version') . ' NPDS</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Vous utilisez NPDS ' . $Version_Sub . ' ' . $Version_Num . '</p>
                        <p>' . adm_translate('Une nouvelle version de NPDS est disponible !') . '</p>
                        <p class="lead mt-3">' . $versus_info[1] . ' ' . $versus_info[2] . '</p>
                        <p class="my-3">
                            <a class="me-3" href="https://github.com/npds/npds_dune/archive/refs/tags/' . $versus_info[2] . '.zip" target="_blank" title="" data-bs-toggle="tooltip" data-bs-original-title="Charger maintenant"><i class="fa fa-download fa-2x me-1"></i>.zip</a>
                            <a class="mx-3" href="https://github.com/npds/npds_dune/archive/refs/tags/' . $versus_info[2] . '.tar.gz" target="_blank" title="" data-bs-toggle="tooltip" data-bs-original-title="Charger maintenant"><i class="fa fa-download fa-2x me-1"></i>.tar.gz</a>
                        </p>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id=""><span id="messageModalIcon" class="me-2"></span><span id="messageModalLabel"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="messageModalContent"></p>
                        <form class="mt-3" id="messageModalForm" action="" method="POST">
                            <input type="hidden" name="id" id="messageModalId" value="0" />
                            <button type="submit" class="btn btn btn-primary btn-sm">' . adm_translate('Confirmer la lecture') . '</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <span class="small text-body-secondary">Information de npds.org</span><img class="adm_img me-2" src="assets/images/admin/message_npds.png" alt="icon_" />
                    </div>
                </div>
            </div>
        </div>';

        echo $adm_ent;

        if ($short_menu_admin != false) {
            echo '</div>';
            return;
        }

        echo '
            <div id="adm_men_corps" class="my-3" >
                <div id="lst_men_main">
                    ' . $bloc_foncts . '
                </div>
            </div>
        </div>';

        return $Q['radminsuper'];
    }

}
