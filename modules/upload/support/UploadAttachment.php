<?php

namespace Modules\Upload\Support;

use App\Support\FileManagement;
use Modules\Upload\Support\UploadStr;
use Modules\Upload\Support\UploadIcon;


class UploadAttachment
{

    /* Fonction de snipe pour l'affichage des fichiers uploadés dans forums */
    public static function display_upload($apli, $post_id, $Mmod)
    {
        $att_size = '';
        $att_type = '';
        $att_name = '';
        $att_url = '';
        $att_link = '';
        $attachments = '';
        $att_icon = '';

        $num_cells = 5;

        $att = self::getAttachments($apli, $post_id, 0, $Mmod);

        if (is_array($att)) {
            $att_count = count($att);

            $attachments = '<div class="list-group">
                <div class="list-group-item d-flex justify-content-start align-items-center mt-2">
                    <img class="n-smil" src="assets/images/forum/subject/07.png" alt="icon_pieces jointes" />
                    <span class="text-body-secondary p-2">' . upload_translate('Pièces jointes') . '</span><a data-bs-toggle="collapse" href="#lst_pj' . $post_id . '"><i data-bs-toggle="tooltip" data-bs-placement="top" title="" class="toggle-icon fa fa-lg me-2 fa-caret-up"></i></a>
                    <span class="badge bg-secondary ms-auto">' . $att_count . '</span>
                </div>
                <div id="lst_pj' . $post_id . '" class="collapse show">';

            for ($i = 0; $i < $att_count; $i++) {
                $att_id        = $att[$i]["att_id"];
                $att_name      = $att[$i]["att_name"];
                $att_path      = $att[$i]["att_path"];
                $att_type      = $att[$i]["att_type"];
                $att_size      = (int) $att[$i]["att_size"];
                $compteur      = $att[$i]["compteur"];
                $visible       = $att[$i]["visible"];
                $att_inline    = $att[$i]["inline"];
                $marqueurV     = (!$visible) ? '@' : '';

                $path = "$att_path/$att_id.$apli." . $marqueurV . "$att_name";

                $att_link      = self::getAttachmentUrl(
                    $apli,
                    $post_id,
                    $att_id,
                    $path,
                    $att_type,
                    $att_size,
                    $att_inline,
                    $compteur,
                    $visible,
                    $Mmod
                );

                $attachments .= $att_link;

                $att_list[$att_id] = $att_name;
            }

            $attachments .= '</div>
            </div>';

            return $attachments;
        }
    }

    /* Fonction qui retourne ou la liste ou l'attachement voulu             */
    public static function getAttachments($apli, $post_id, $att_id = 0, $Mmod = 0)
    {
        global $upload_table;

        $query = "SELECT att_id, att_name, att_type, att_size, att_path, inline, compteur, visible 
                    FROM $upload_table 
                    WHERE apli='$apli' AND post_id='$post_id'";

        if ($att_id > 0) {
            $query .= " AND att_id=$att_id";
        }

        if (!$Mmod) {
            $query .= " AND visible=1";
        }

        $query .= " ORDER BY att_type,att_name";
        $result = sql_query($query);

        $i = 0;

        while ($attach = sql_fetch_assoc($result)) {
            $att[$i] = $attach;
            $i++;
        }

        return ($i == 0) ? '' : $att;
    }

    /* Retourne Le mode d'affichage pour un attachement                     */
    /* 1   display as icon (link)                                           */
    /* 2   display as image                                                 */
    /* 3   display as embedded HTML text or the source                      */
    /* 4   display as embedded text, PRE-formatted                          */
    /* 5   display as flash animation                                       */
    public static function getAttDisplayMode($att_type, $att_inline = 'A')
    {
        global $mime_dspfmt, $mimetype_default;

        load_mime_types();

        if ($att_inline) {
            $display_mode = (isset($mime_dspfmt[$att_type])) ? $mime_dspfmt[$att_type] : $mime_dspfmt[$mimetype_default];
        } else {
            $display_mode = ATT_DSP_LINK;
        }

        return $display_mode;
    }

    public function displayMode(bool $att_inline, int $att_type): int
    {
        return $att_inline
            ? (UploadMineType::displayMimeType($att_type) ?? UploadMineType::LINK)
            : UploadMineType::LINK;
    }

    // Retourne l'attachement
    public static function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod): string
    {
        $result = self::resolveAttachment($att_path);

        // Ici $result contient ton message d’erreur HTML
        if (is_string($result)) {
            return $result;
        }

        $att_path = $result['path'];
        $att_name = $result['name'];
        $att_name = $result['file'];

        if ($Mmod) {
            global $userdata;
            $marqueurM = '&amp;Mmod=' . substr($userdata[2], 8, 6);
        } else {
            $marqueurM = '';
        }

        $att_url = 'getfile.php?att_id='.$att_id.'&amp;apli='.$apli . $marqueurM . '&amp;att_name=' . rawurlencode($att_name);

        if ($visible != 1) {
            $visible_wrn = '&nbsp;<span class="text-danger" style="font-size: .65rem;">' . upload_translate('Fichier non visible') . '</span>';
        }

        $mode = self::displayMode($att_inline, $att_type);

        return match ($mode) {
            // display as an embedded image
            ATT_DSP_IMG => UploadMineRender::renderImg([
                'name'    => $att_name,
                'url'     => $att_url,
                'visible' => $visible_wrn ?? ''
            ]),

            // display as embedded text, PRE-formatted
            ATT_DSP_PLAINTEXT => UploadMineRender::renderText([
                'name'    => $att_name,
                'path'    => $att_path,
                'visible' => $visible_wrn ?? ''
            ]),

            // display as embedded HTML text
            ATT_DSP_HTML => UploadMineRender::renderHtml([
                'name'    => $att_name,
                'path'    => $att_path,
                'visible' => $visible_wrn ?? ''
            ]),

            // Embedded Macromedia Shockwave Flash
            ATT_DSP_SWF => UploadMineRender::renderShockwaveFlash([
                'url'     => $att_url,
                'path'    => $att_path,
                'visible' => $visible_wrn ?? ''
            ]),

            // display in a <video> html5 tag
            ATT_DSP_VIDEO => UploadMineRender::renderVideo([
                'name'    => $att_name,
                'url'     => $att_url
            ]),

            // display in a <audio> html5 tag
            ATT_DSP_AUDIO => UploadMineRender::renderAudio([
                'name'    => $att_name,
                'url'     => $att_url
            ]),

            // display as link
            default => UploadMineRender::renderLink([
                'name'     => $att_name,
                'url'      => $att_url,
                'type'     => $att_type,
                'size'     => $att_size,
                'visible'  => $visible_wrn ?? '',
                'compteur' => $compteur,
            ]),
        };
    }

    public static function resolveAttachment(string $att_path): array|string
    {
        global $DOCUMENTROOT;

        $base_name  = basename($att_path);                               // ex: report.pdf
        $att_name   = pathinfo($base_name, PATHINFO_FILENAME);            // ex: report
        $full_path  = $DOCUMENTROOT . $att_path;

        if (!is_file($full_path)) {
            return '&nbsp;<span class="text-danger" style="font-size: .65rem;">'
                . upload_translate('Fichier non trouvé') . ' : ' . $base_name . '</span>';
        }

        return [
            'path' => $full_path,  // chemin absolu pour ouvrir le fichier
            'name' => $att_name,   // nom sans extension (utile pour affichage)
            'file' => $base_name    // nom complet avec extension
        ];
    }


    public function displayMode__deprecated(bool $att_inline, int $att_type): int
    {
        if ($att_inline) {

            $type = UploadMineType::displayMimeType($att_type);

            if (isset($type)) {
                $mode = $type;
            } else {
                $mode = UploadMineType::displayMimeType(UploadMineType::LINK);

            }
        } else {
            $mode = UploadMineType::LINK;
        }

        return $mode;
    }

    public static function insertAttachment($apli, $IdPost, $IdTopic, $IdForum, $name, $path, $inline = 'A', $size = 0, $type = '')
    {
        global $upload_table, $visible_forum;

        $size = empty($size) ? filesize($path) : $size;
        $type = empty($type) ? 'application/octet-stream' : $type;

        $stamp = time();

        $sql = "INSERT INTO $upload_table 
                VALUES (0, '$IdPost', '$IdTopic','$IdForum', '$stamp', '$name', '$type', '$size', '$path', '1', '$apli', '0', '$visible_forum')";
        $ret = sql_query($sql);

        if (!$ret) {
            return -1;
        }

        return sql_last_id();
    }

    /* Suprime l'attachement dans la base de données en cas d'erreur d'upload */
    public static function deleteAttachment($apli, $IdPost, $upload_dir, $id, $att_name)
    {
        global $upload_table;

        @unlink("$upload_dir/$id.$apli.$att_name");

        $sql = "DELETE FROM $upload_table 
                WHERE att_id= '$id'";

        sql_query($sql);
    }

    /* Effacer les fichier joint demander                */
    public static function delete($del_att)
    {
        global $upload_table, $apli, $DOCUMENTROOT;

        $rep = $DOCUMENTROOT;

        $del_att = is_array($del_att) ? implode(',', $del_att) : $del_att;

        $sql = "SELECT att_id, att_name, att_path 
                FROM $upload_table 
                WHERE att_id IN ($del_att)";

        $result = sql_query($sql);

        while (list($att_id, $att_name, $att_path) = sql_fetch_row($result)) {
            @unlink($rep . "$att_path/$att_id.$apli.$att_name");
        }

        $sql = "DELETE FROM $upload_table 
                WHERE att_id IN ($del_att)";

        sql_query($sql);
    }

    /* Update le type d'affichage                        */
    public static function update_inline($inline_att)
    {
        global $upload_table;

        if (is_array($inline_att)) {
            foreach ($inline_att as $id => $mode) {
                $sql = "UPDATE $upload_table 
                        SET inline='$mode' 
                        WHERE att_id=$id";

                sql_query($sql);
            }
        }
    }

    /* Update la visibilité                              */
    public static function renomme_fichier($listeV, $listeU)
    {
        global $upload_table, $apli, $DOCUMENTROOT;

        $query = "SELECT att_id, att_name, att_path 
                FROM $upload_table 
                WHERE att_id in ($listeV) 
                AND visible=1";

        $result = sql_query($query);

        while ($attach = sql_fetch_assoc($result)) {
            if (!file_exists($DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.' . $attach['att_name'])) {
                rename($DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.@' . $attach['att_name'], $DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.' . $attach['att_name']);
            }
        }

        $query = "SELECT att_id, att_name, att_path 
                FROM $upload_table 
                WHERE att_id IN ($listeU) 
                AND visible=0";

        $result = sql_query($query);

        while ($attach = sql_fetch_assoc($result)) {
            if (!file_exists($DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.@' . $attach['att_name'])) {
                rename($DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.' . $attach['att_name'], $DOCUMENTROOT . $attach['att_path'] . $attach['att_id'] . '.' . $apli . '.@' . $attach['att_name']);
            }
        }
    }


    public static function update_visibilite($visible_att, $visible_list)
    {
        global $upload_table;

        if (is_array($visible_att)) {
            $visible = implode(',', $visible_att);

            $sql = "UPDATE $upload_table 
                    SET visible='1' 
                    WHERE att_id IN ($visible)";

            sql_query($sql);

            $visible_lst = explode(',', substr($visible_list, 0, strlen($visible_list) - 1));

            $result = array_diff($visible_lst, $visible_att);

            $unvisible = implode(",", $result);

            $sql = "UPDATE $upload_table 
                    SET visible='0' 
                    WHERE att_id IN ($unvisible)";

            sql_query($sql);
        } else {
            $visible_lst = explode(',', substr($visible_list, 0, strlen($visible_list) - 1));

            $unvisible = implode(',', $visible_lst);

            $sql = "UPDATE $upload_table 
                    SET visible='0' 
                    WHERE att_id IN ($unvisible)";

            sql_query($sql);
        }

        self::renomme_fichier($visible, $unvisible);
    }
}
