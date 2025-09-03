<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2024 by Philippe Brunier                     */
/* Copyright Snipe 2003  base sources du forum w-agora de Marc Druilhe  */
/************************************************************************/
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

use App\Library\FileManagement\FileManagement;

if (preg_match('#upload\.func\.forum\.php#', $_SERVER['PHP_SELF'])) {
    die();
}

if (!isset($upload_conf)) {
    include_once 'modules/upload/language/' . $language . '/upload.lang-' . $language . '.php';
    include_once 'modules/upload/config/upload.conf.forum.php';
    include_once 'library/file/File.php';
}

/************************************************************************/
/* Fonction pour charger en mémoire les mimetypes                       */
/************************************************************************/
function load_mimetypes()
{
    global $mimetypes, $mimetype_default, $mime_dspinl, $mime_dspfmt, $mime_renderers, $att_icons, $att_icon_default, $att_icon_multiple;

    if (defined('ATT_DSP_LINK')) {
        return;
    }

    if (file_exists('modules/upload/support/mimetypes.php')) {
        include 'modules/upload/support/mimetypes.php';
    }
}

/************************************************************************/
/* Fonction qui retourne ou la liste ou l'attachement voulu             */
/************************************************************************/
function getAttachments($apli, $post_id, $att_id = 0, $Mmod = 0)
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

/************************************************************************/
/* Fonction permettant de créer une checkbox                            */
/************************************************************************/
function getCheckBox($name, $value = 1, $current, $text = '', $cla = ' ')
{
    $p =  sprintf(
        '<input class="form-check-input ' . $cla . '" type="checkbox" name="%s" value="%s"%s />%s',
        $name,
        $value,
        ("$current" == "$value") ? ' checked="checked"' : '',
        (empty($text)) ? '' : " $text"
    );

    return $p;
}

/************************************************************************/
/* Fonction permettant une liste de choix                               */
/************************************************************************/
function getListBox($name, $items, $selected = '', $multiple = 0, $onChange = '')
{
    $oc = empty($onChange) ? '' : ' onchange="' . $onChange . '"';

    $p = sprintf(
        '<select class="form-select form-select-sm mx-auto" name="%s%s"%s%s>',
        $name,
        ($multiple == 1) ? '[]' : '',
        ($multiple == 1) ? ' multiple' : '',
        $oc
    );

    if (is_array($items)) {
        foreach ($items as $k => $v) {
            $p .= sprintf('<option value="%s"%s>%s</option>', $k, strcmp($selected, $k) ? '' : ' selected="selected"', $v);
        }
    }

    return $p . '</select>';
}

/************************************************************************/
/* Pour la class                                                        */
/************************************************************************/
/************************************************************************/
/* Ajoute l'attachement dans la base de données                         */
/************************************************************************/
function insertAttachment($apli, $IdPost, $IdTopic, $IdForum, $name, $path, $inline = 'A', $size = 0, $type = '')
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

/************************************************************************/
/* Suprime l'attachement dans la base de données en cas d'erreur d'upload */
/************************************************************************/
function deleteAttachment($apli, $IdPost, $upload_dir, $id, $att_name)
{
    global $upload_table;

    @unlink("$upload_dir/$id.$apli.$att_name");

    $sql = "DELETE FROM $upload_table 
            WHERE att_id= '$id'";

    sql_query($sql);
}

/************************************************************************/
/* Pour la visualisation dans les forums                                */
/************************************************************************/
/* Fonction de snipe pour l'affichage des fichiers uploadés dans forums */
/************************************************************************/
function display_upload($apli, $post_id, $Mmod)
{
    $att_size = '';
    $att_type = '';
    $att_name = '';
    $att_url = '';
    $att_link = '';
    $attachments = '';
    $att_icon = '';

    $num_cells = 5;

    $att = getAttachments($apli, $post_id, 0, $Mmod);

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

            $att_link      = getAttachmentUrl($apli, $post_id, $att_id, "$att_path/$att_id.$apli." . $marqueurV . "$att_name", $att_type, $att_size, $att_inline, $compteur, $visible, $Mmod);

            $attachments .= $att_link;
            $att_list[$att_id] = $att_name;
        }

        $attachments .= '</div>
        </div>';

        return $attachments;
    }
}

/************************************************************************/
/* Retourne Le mode d'affichage pour un attachement                     */
/* 1   display as icon (link)                                           */
/* 2   display as image                                                 */
/* 3   display as embedded HTML text or the source                      */
/* 4   display as embedded text, PRE-formatted                          */
/* 5   display as flash animation                                       */
/************************************************************************/
function getAttDisplayMode($att_type, $att_inline = 'A')
{
    global $mime_dspfmt, $mimetype_default, $ext;

    load_mimetypes();

    if ($att_inline) {
        $display_mode = (isset($mime_dspfmt[$att_type])) ? $mime_dspfmt[$att_type] : $mime_dspfmt[$mimetype_default];
    } else {
        $display_mode = ATT_DSP_LINK;
    }

    return $display_mode;
}

/************************************************************************/
/* Retourne l'icon                                                      */
/************************************************************************/
function att_icon($filename)
{
    global $att_icons, $att_icon_default, $att_icon_multiple;

    load_mimetypes();

    $suffix = strtoLower(substr(strrchr($filename, '.'), 1));

    return (isset($att_icons[$suffix])) ? $att_icons[$suffix] : $att_icon_default;
}

/************************************************************************/
/* Partie Graphique                                                     */
/************************************************************************/
/* Controle la taille de l'image à afficher                             */
/************************************************************************/
function verifsize($size)
{
    $width_max = 500;
    $height_max = 500;

    if ($size[0] == 0) {
        $size[0] = ceil($width_max / 3);
    }

    if ($size[1] == 0) {
        $size[1] = ceil($height_max / 3);
    }

    $width = $size[0];
    $height = $size[1];

    if ($width > $width_max) {
        $imageProp = ($width_max * 100) / $width;
        $height = ceil(($height * $imageProp) / 100);
        $width = $width_max;
    }

    if ($height > $height_max) {
        $imageProp = ($height_max * 100) / $height;
        $width = ceil(($width * $imageProp) / 100);
        $height = $height_max;
    }

    return ('width="' . $width . '" height="' . $height . '"');
}

/************************************************************************/
/* Retourne l'attachement                                               */
/************************************************************************/
function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod)
{
    global $icon_dir, $img_dir, $forum;
    global $mimetype_default, $mime_dspfmt, $mime_renderers;
    global $DOCUMENTROOT;

    load_mimetypes();

    $att_name = substr(strstr(basename($att_path), '.'), 1);
    $att_name = substr(strstr(basename($att_name), '.'), 1);

    $att_path = $DOCUMENTROOT . $att_path;

    if (!is_file($att_path))
        return '&nbsp;<span class="text-danger" style="font-size: .65rem;">' . upload_translate('Fichier non trouvé') . ' : ' . $att_name . '</span>';

    /*

    // display mode if displayed inline
    define('ATT_DSP_LINK', '1');        // displays as link (icon)
    define('ATT_DSP_IMG', '2');         // display inline as a picture, using <img> tag.
    define('ATT_DSP_HTML', '3');        // display inline as HTML, e.g. banned tags are stripped.
    define('ATT_DSP_PLAINTEXT', '4');   // display inline as text, using <pre> tag.
    define('ATT_DSP_SWF', '5');         // Embedded Macromedia Shockwave Flash
    define('ATT_DSP_VIDEO', '6');       // video display inline in a video html5 tag 
    define('ATT_DSP_AUDIO', '7');       // audio display inline in a audio html5 tag

    $mime_dspfmt[$mimetype_default] = ATT_DSP_LINK;

    // display mode if displayed inline
    $mime_dspfmt['image/gif'] = ATT_DSP_IMG;
    $mime_dspfmt['image/bmp'] = ATT_DSP_LINK;
    $mime_dspfmt['image/png'] = ATT_DSP_IMG;
    $mime_dspfmt['image/x-png'] = ATT_DSP_IMG;
    $mime_dspfmt['image/jpeg'] = ATT_DSP_IMG;
    $mime_dspfmt['image/pjpeg'] = ATT_DSP_IMG;
    $mime_dspfmt['image/svg+xml'] = ATT_DSP_IMG;
    $mime_dspfmt['text/html'] = ATT_DSP_HTML;
    $mime_dspfmt['text/plain'] = ATT_DSP_PLAINTEXT;
    $mime_dspfmt['application/x-shockwave-flash'] = ATT_DSP_SWF;
    $mime_dspfmt['video/mpeg'] = ATT_DSP_VIDEO;
    $mime_dspfmt['audio/mpeg'] = ATT_DSP_AUDIO;
    */

    if ($att_inline) { // $att_inline ==> exemple sur un insert en base de donner :  1 
        if (isset($mime_dspfmt[$att_type])) { // $att_type ==> exemple sur un insert en base de donner : 'image/jpeg'
            $display_mode = $mime_dspfmt[$att_type]; // $att_type ==> exemple sur un insert en base de donner : 'image/jpeg'
            // donc ici : $display_mode = 'image/jpeg'
        } else {
            $display_mode = $mime_dspfmt[$mimetype_default]; // ==> config : $mimetype_default = 'application/octet-stream';
            // donc ici : $display_mode = 'application/octet-stream'
        }
    } else {
        $display_mode = ATT_DSP_LINK; // ==> define('ATT_DSP_LINK', '1');        // displays as link (icon)
        // donc ici : $display_mode = '1'

    }

    if ($Mmod) {
        global $userdata;
        $marqueurM = '&amp;Mmod=' . substr($userdata[2], 8, 6);
    } else {
        $marqueurM = '';
    }

    $att_url = "getfile.php?att_id=$att_id&amp;apli=$apli" . $marqueurM . "&amp;att_name=" . rawurlencode($att_name);

    settype($visible_wrn, 'string');

    if ($visible != 1) {
        $visible_wrn = '&nbsp;<span class="text-danger" style="font-size: .65rem;">' . upload_translate('Fichier non visible') . '</span>';
    }

    switch ($display_mode) {

        case ATT_DSP_IMG: // display as an embedded image
            $size = @getImageSize("$att_path");

            $img_size = 'style="width: 100%; height:auto;" loading="lazy" ';

            $mime_renderers[ATT_DSP_IMG]       = "
                            <div class=\"list-group-item list-group-item-action flex-column align-items-start\">
                            <code>\$att_name</code>
                            <a href=\"javascript:void(0);\" onclick=\"window.open('\$att_url','fullsizeimg','menubar=no,location=no,directories=no,status=no,copyhistory=no,height=600,width=800,toolbar=no,scrollbars=yes,resizable=yes');\"><img src=\"\$att_url\" alt=\"\$att_name\" \$img_size />\$visible_wrn </a>
                            </div>";


            $text = str_replace('"', '\"', $mime_renderers[ATT_DSP_IMG]);

            eval("\$ret=stripSlashes(\"$text\");");
            break;

        case ATT_DSP_PLAINTEXT: // display as embedded text, PRE-formatted
            $att_contents = str_replace("\\", "\\\\", htmlSpecialChars(join('', file($att_path)), ENT_COMPAT | ENT_HTML401, 'UTF-8'));

            $att_contents = word_wrap($att_contents);

            $mime_renderers[ATT_DSP_PLAINTEXT] = "
                            <div class=\"list-group-item flex-column align-items-start\">
                            <div class=\"py-2 mb-2\"><code>\$att_name\$visible_wrn</code></div>
                            <div style=\"width:100%; \">
                                <pre>\$att_contents</pre>
                            </div>
                            </div>";

            $text = str_replace('"', '\"', $mime_renderers[ATT_DSP_PLAINTEXT]);

            eval("\$ret=\"$text\";");
            break;

        case ATT_DSP_HTML: // display as embedded HTML text
            //au choix la source ou la page

            $att_contents = word_wrap(nl2br(scr_html(join("", file($att_path)))));

            //$att_contents = removeHack (join ("", file ($att_path)));

            $mime_renderers[ATT_DSP_HTML]      = "
                            <table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
                            <tr>
                                <td style=\"background-color: #000000;\">
                                    <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\" width=\"100%\">
                                        <tr>
                                        <td align=\"center\" style=\"background-color: #cccccc;\">\$att_name\$visible_wrn</td>
                                        </tr>
                                        <tr>
                                        <td style=\"background-color: #ffffff;\">\$att_contents</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            </table>";

            $text = str_replace('"', '\"', $mime_renderers[ATT_DSP_HTML]);

            eval("\$ret=stripSlashes(\"$text\");");
            break;

        case ATT_DSP_SWF: // Embedded Macromedia Shockwave Flash
            $size = @getImageSize("$att_path");

            $img_size = verifsize($size);

            $mime_renderers[ATT_DSP_SWF]       = "
                            <p align=\"center\">
                            <object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4\,0\,2\,0\" \$img_size><param name=\"quality\" value=\"high\"><param name=\"SRC\" value=\"\$att_url\"><embed src=\"\$att_url\" quality=\"high\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" \$img_size></embed></object>\$visible_wrn
                            </p>";

            $text = str_replace('"', '\"', $mime_renderers[ATT_DSP_SWF]);

            eval("\$ret=stripSlashes(\"$text\");");
            break;

        case ATT_DSP_VIDEO: // display in a <video> html5 tag
            $img_size = 'width="100%" height="auto" ';

            $mime_renderers[ATT_DSP_VIDEO]     = "
                            <div class=\"list-group-item list-group-item-action flex-column align-items-start\"><code>\$att_name</code>
                            <div>
                                <video playsinline preload=\"metadata\" muted controls \$img_size >
                                    <source src=\"\$att_url\" type=\"video/mp4\">
                                </video>
                            </div>
                            </div>";

            $text = str_replace('"', '\"', $mime_renderers[ATT_DSP_VIDEO]);

            eval("\$ret=stripSlashes(\"$text\");");
            break;

        case ATT_DSP_AUDIO: // display in a <audio> html5 tag
            $img_size = 'width="100%" height="auto" ';

            $mime_renderers[ATT_DSP_AUDIO]    = "
                            <div class=\"list-group-item list-group-item-action flex-column align-items-start\"><code>\$att_name</code>
                            <div>
                                <audio controls src=\"\$att_url\"></audio><br />
                            </div>
                            </div>";

            $text = str_replace('"', '\"', $mime_renderers[ATT_DSP_AUDIO]);

            eval("\$ret=stripSlashes(\"$text\");");
            break;

        default: // display as link
            $Fichier = new FileManagement;

            $att_size = $Fichier->fileSizeFormat($att_size, 1);

            $att_icon = att_icon($att_name);

            $mime_renderers[ATT_DSP_LINK]      = "
                            <a class=\"list-group-item list-group-item-action d-flex justify-content-start align-items-center\" href=\"\$att_url\" target=\"_blank\" >\$att_icon<span title=\"" . upload_translate("Télécharg.") . " \$att_name (\$att_type - \$att_size)\" data-bs-toggle=\"tooltip\" style=\"font-size: .85rem;\" class=\"ms-2 n-ellipses\"><strong>&nbsp;\$att_name</strong></span><span class=\"badge bg-secondary ms-auto\" style=\"font-size: .75rem;\">\$compteur &nbsp;<i class=\"fa fa-lg fa-download\"></i></span><br /><span align=\"center\">\$visible_wrn</span></a>";


            $text = str_replace('"', '\"', $mime_renderers[ATT_DSP_LINK]);

            eval("\$ret=stripSlashes(\"$text\");");
            break;
    }

    // retour eval
    return $ret;
}

/************************************************************************/
/* Fonction d'affichage des fichier text directement                    */
/************************************************************************/
/* Copyright 1999 Dominic J. Eidson, use as you wish, but give credit   */
/* where credit due.                                                    */
/************************************************************************/
function word_wrap($string, $cols = 80, $prefix = '')
{
    $t_lines = explode("\n", $string);

    $outlines = '';

    foreach ($t_lines as $thisline) {
        if (strlen($thisline) > $cols) {

            $newline = '';
            $t_l_lines = explode(' ', $thisline);

            foreach ($t_l_lines as $thisword) {
                while ((strlen($thisword) + strlen($prefix)) > $cols) {
                    $cur_pos = 0;
                    $outlines .= $prefix;

                    for ($num = 0; $num < $cols - 1; $num++) {
                        $outlines .= $thisword[$num];
                        $cur_pos++;
                    }

                    $outlines .= "\n";
                    $thisword = substr($thisword, $cur_pos, (strlen($thisword) - $cur_pos));
                }

                if ((strlen($newline) + strlen($thisword)) > $cols) {
                    $outlines .= $prefix . $newline . "\n";
                    $newline = $thisword . ' ';
                } else {
                    $newline .= $thisword . ' ';
                }
            }
            $outlines .= $prefix . $newline . "\n";
        } else {
            $outlines .= $prefix . $thisline . "\n";
        }
    }

    return $outlines;
}

/***********************************************/
/* Affiche la source d'une page html           */
/***********************************************/
function scr_html($text)
{
    $text = str_replace('<', '&lt;', $text);
    $text = str_replace('>', '&gt;', $text);

    return $text;
}

/*****************************************************/
/* Effacer les fichier joint demander                */
/*****************************************************/
function delete($del_att)
{
    global $upload_table, $rep_upload_forum, $apli, $DOCUMENTROOT;

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

/*****************************************************/
/* Update le type d'affichage                        */
/*****************************************************/
function update_inline($inline_att)
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

/*****************************************************/
/* Update la visibilité                              */
/*****************************************************/
function renomme_fichier($listeV, $listeU)
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

function update_visibilite($visible_att, $visible_list)
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

    renomme_fichier($visible, $unvisible);
}
