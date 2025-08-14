<?php

if (! function_exists('adminblock'))
{ 
    #autodoc adminblock() : Bloc Admin <br />=> syntaxe : function#adminblock
    function adminblock()
    {
        global $admin, $aid, $admingraphic, $adminimg, $admf_ext, $Version_Sub, $Version_Num, $nuke_url;

        $bloc_foncts_A = '';

        if ($admin) {
            $Q = sql_fetch_assoc(sql_query("SELECT * 
                                            FROM " . sql_prefix('authors') . " 
                                            WHERE aid='$aid' LIMIT 1"));

            $R = $Q['radminsuper'] == 1
                ? sql_query("SELECT * 
                            FROM " . sql_prefix('fonctions') . " f 
                            WHERE f.finterface =1 
                            AND f.fetat != '0' 
                            ORDER BY f.fcategorie")

                : sql_query("SELECT * 
                            FROM " . sql_prefix('fonctions') . " f 
                            LEFT JOIN " . sql_prefix('droits') . " d ON f.fdroits1 = d.d_fon_fid 
                            LEFT JOIN " . sql_prefix('') . "authors a ON d.d_aut_aid =a.aid 
                            WHERE f.finterface =1 
                            AND fetat!=0 
                            AND d.d_aut_aid='$aid' 
                            AND d.d_droits REGEXP'^1' 
                            ORDER BY f.fcategorie");

            while ($SAQ = sql_fetch_assoc($R)) {
                $arraylecture = array();

                if (isset($SAQ['fdroits1_descr']) && is_string($SAQ['fdroits1_descr'])) {
                    $arraylecture = explode('|', $SAQ['fdroits1_descr']);
                }

                $cat[]      = $SAQ['fcategorie'];
                $cat_n[]    = $SAQ['fcategorie_nom'];
                $fid_ar[]   = $SAQ['fid'];

                if ($SAQ['fcategorie'] == 9) {
                    $adminico = $adminimg . $SAQ['ficone'] . '.' . $admf_ext;
                }

                if ($SAQ['fcategorie'] == 9 and strstr($SAQ['furlscript'], "op=Extend-Admin-SubModule"))
                    if (file_exists('modules/' . $SAQ['fnom'] . '/' . $SAQ['fnom'] . '.' . $admf_ext)) {
                        $adminico = 'modules/' . $SAQ['fnom'] . '/' . $SAQ['fnom'] . '.' . $admf_ext;
                    } else {
                        $adminico = $adminimg . 'module.' . $admf_ext;
                    }

                if ($SAQ['fcategorie'] == 9) {
                    if (preg_match('#messageModal#', $SAQ['furlscript'])) {
                        $furlscript = 'data-bs-toggle="modal" data-bs-target="#bl_messageModal"';
                    }

                    if (preg_match('#mes_npds_\d#', $SAQ['fnom'])) {
                        if (!in_array($aid, $arraylecture, true)) {
                            $bloc_foncts_A .= '
                            <a class=" btn btn-outline-primary btn-sm me-2 my-1 tooltipbyclass" title="' . $SAQ['fretour_h'] . '" data-id="' . $SAQ['fid'] . '" data-bs-html="true" ' . $furlscript . ' >
                                <img class="adm_img" src="' . $adminico . '" alt="icon_message" loading="lazy" />
                                <span class="badge bg-danger ms-1">' . $SAQ['fretour'] . '</span>
                            </a>';
                        }
                    } else {
                        $furlscript = preg_match('#versusModal#', $SAQ['furlscript'])
                            ? 'data-bs-toggle="modal" data-bs-target="#bl_versusModal"'
                            : $SAQ['furlscript'];

                        if (preg_match('#NPDS#', $SAQ['fretour_h'])) {
                            $SAQ['fretour_h'] = str_replace('NPDS', 'NPDS^', $SAQ['fretour_h']);
                        }

                        $bloc_foncts_A .=
                            '<a class=" btn btn-outline-primary btn-sm me-2 my-1 tooltipbyclass" title="' . $SAQ['fretour_h'] . '" data-id="' . $SAQ['fid'] . '" data-bs-html="true" ' . $furlscript . ' >
                            <img class="adm_img" src="' . $adminico . '" alt="icon_' . $SAQ['fnom_affich'] . '" loading="lazy" />
                            <span class="badge bg-danger ms-1">' . $SAQ['fretour'] . '</span>
                        </a>';
                    }
                }
            }

            $result = sql_query("SELECT title, content 
                                FROM " . sql_prefix('block') . " 
                                WHERE id=2");

            list($title, $content) = sql_fetch_row($result);

            global $block_title;
            $title = $title == '' ? $block_title : aff_langue($title);

            $content = aff_langue(preg_replace_callback('#<a href=[^>]*(&)[^>]*>#', 'changetoampadm', $content));

            //==> recuperation
            $messagerie_npds = file_get_contents('https://raw.githubusercontent.com/npds/npds_dune/master/versus.txt');
            $messages_npds = explode("\n", $messagerie_npds);

            array_pop($messages_npds);

            // traitement spécifique car fonction permanente versus
            $versus_info = explode('|', $messages_npds[0]);

            if ($versus_info[1] == $Version_Sub and $versus_info[2] == $Version_Num) {
                sql_query("UPDATE " . sql_prefix('fonctions') . " 
                        SET fetat='1', fretour='', fretour_h='Version NPDS " . $Version_Sub . " " . $Version_Num . "', furlscript='' 
                        WHERE fid='36'");
            } else {
                sql_query("UPDATE " . sql_prefix('fonctions') . " 
                        SET fetat='1', fretour='N', furlscript='data-bs-toggle=\"modal\" data-bs-target=\"#versusModal\"', fretour_h='Une nouvelle version NPDS est disponible !<br />" . $versus_info[1] . " " . $versus_info[2] . "<br />Cliquez pour télécharger.' 
                        WHERE fid='36'");
            }

            $content .= '<div class="d-flex justify-content-start flex-wrap" id="adm_block">
            ' . $bloc_foncts_A;

            if ($Q['radminsuper'] == 1) {
                $content .= '<a class="btn btn-outline-primary btn-sm me-2 my-1" title="' . translate('Vider la table chatBox') . '" data-bs-toggle="tooltip" href="powerpack.php?op=admin_chatbox_write&amp;chatbox_clearDB=OK" ><img src="images/admin/chat.png" class="adm_img" alt="icon clear chat" loading="lazy" />&nbsp;<span class="badge bg-danger ms-1">X</span></a>';
            }

            $content .= '</div>
            <div class="mt-3">
                <small class="text-body-secondary"><i class="fas fa-user-cog fa-2x align-middle"></i> ' . $aid . '</small>
            </div>
            <div class="modal fade" id="bl_versusModal" tabindex="-1" aria-labelledby="bl_versusModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bl_versusModalLabel"><img class="adm_img me-2" src="assets/images/admin/message_npds.png" alt="icon_" loading="lazy" />' . translate('Version') . ' NPDS^</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Vous utilisez NPDS^ ' . $Version_Sub . ' ' . $Version_Num . '</p>
                        <p>' . translate('Une nouvelle version de NPDS^ est disponible !') . '</p>
                        <p class="lead mt-3">' . $versus_info[1] . ' ' . $versus_info[2] . '</p>
                        <p class="my-3">
                            <a class="me-3" href="https://github.com/npds/npds_dune/archive/refs/tags/' . $versus_info[2] . '.zip" target="_blank" title="" data-bs-toggle="tooltip" data-original-title="Charger maintenant"><i class="fa fa-download fa-2x me-1"></i>.zip</a>
                            <a class="mx-3" href="https://github.com/npds/npds_dune/archive/refs/tags/' . $versus_info[2] . '.tar.gz" target="_blank" title="" data-bs-toggle="tooltip" data-original-title="Charger maintenant"><i class="fa fa-download fa-2x me-1"></i>.tar.gz</a>
                        </p>
                    </div>
                    <div class="modal-footer">
                    </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="bl_messageModal" tabindex="-1" aria-labelledby="bl_messageModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id=""><span id="bl_messageModalIcon" class="me-2"></span><span id="bl_messageModalLabel"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="bl_messageModalContent"></p>
                        <form class="mt-3" id="bl_messageModalForm" action="" method="POST">
                            <input type="hidden" name="id" id="bl_messageModalId" value="0" />
                            <button type="submit" class="btn btn btn-primary btn-sm">' . translate('Confirmer la lecture') . '</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <span class="small text-body-secondary">Information de npds.org</span><img class="adm_img me-2" src="assets/images/admin/message_npds.png" alt="icon_" loading="lazy" />
                    </div>
                    </div>
                </div>
            </div>
            <script>
                $(function () {
                $("#bl_messageModal").on("show.bs.modal", function (event) {
                    var button = $(event.relatedTarget); 
                    var id = button.data("id");
                    $("#bl_messageModalId").val(id);
                    $("#bl_messageModalForm").attr("action", "' . $nuke_url . '/admin.php?op=alerte_update");
                    $.ajax({
                        url:"' . $nuke_url . '/admin.php?op=alerte_api",
                        method: "POST",
                        data:{id:id},
                        dataType:"JSON",
                        success:function(data) {
                            var fnom_affich = JSON.stringify(data["fnom_affich"]),
                                fretour_h = JSON.stringify(data["fretour_h"]),
                                ficone = JSON.stringify(data["ficone"]);
                            $("#bl_messageModalLabel").html(JSON.parse(fretour_h));
                            $("#bl_messageModalContent").html(JSON.parse(fnom_affich));
                            $("#bl_messageModalIcon").html("<img src=\"assets/images/admin/"+JSON.parse(ficone)+".png\" />");
                        }
                    });
                    });
                });
            </script>';

            themesidebox($title, $content);
        }
    }
}
