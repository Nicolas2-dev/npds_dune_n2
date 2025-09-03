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

/*
CREATE TABLE `forum_attachments` (
  `att_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `topic_id` int(11) NOT NULL DEFAULT 0,
  `forum_id` int(11) NOT NULL DEFAULT 0,
  `unixdate` int(11) NOT NULL DEFAULT 0,
  `att_name` varchar(255) NOT NULL DEFAULT '',
  `att_type` varchar(64) NOT NULL DEFAULT '',
  `att_size` int(11) NOT NULL DEFAULT 0,
  `att_path` varchar(255) NOT NULL DEFAULT '',
  `inline` char(1) NOT NULL DEFAULT '',
  `apli` varchar(10) NOT NULL DEFAULT '',
  `compteur` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `forum_attachments`
--

INSERT INTO `forum_attachments` (`att_id`, `post_id`, `topic_id`, `forum_id`, `unixdate`, `att_name`, `att_type`, `att_size`, `att_path`, `inline`, `apli`, `compteur`, `visible`) VALUES
(8, 8, 1, 1, 1756836830, 'XTDK0173.JPG', 'image/jpeg', 3178729, '/modules/upload/upload_forum/', '1', 'forum_npds', 0, 1),
(9, 8, 1, 1, 1756836865, 'YAEV4735.JPG', 'image/jpeg', 5244279, '/modules/upload/upload_forum/', '1', 'forum_npds', 0, 1);

*/


// display mode if displayed inline
define('ATT_DSP_LINK', '1');        // displays as link (icon)
define('ATT_DSP_IMG', '2');         // display inline as a picture, using <img> tag.
define('ATT_DSP_HTML', '3');        // display inline as HTML, e.g. banned tags are stripped.
define('ATT_DSP_PLAINTEXT', '4');   // display inline as text, using <pre> tag.
define('ATT_DSP_SWF', '5');         // Embedded Macromedia Shockwave Flash
define('ATT_DSP_VIDEO', '6');       // video display inline in a video html5 tag 
define('ATT_DSP_AUDIO', '7');       // audio display inline in a audio html5 tag

// ==> getfile.php
// ==> upConfigure($ModPath, $ModStart, $f_meta_nom, $f_titre, $adminimg)
// ==> function load_mimetypes()
// ==> function editeur_upload()
// ==> function forum_upload()
// ==> FileUpload => function uploadFile($IdPost, $IdTopic, $name, $size, $type, $src_file, $inline = DEFAULT_INLINE)
$mimetypes = array(
    'avi'   => 'video/x-msvideo',
    'bat'   => 'text/plain',
    'bak'   => 'text/plain',
    'bmp'   => 'image/bmp',
    'gif'   => 'image/gif',
    'gz'    => 'application/x-gzip',
    'htm'   => 'text/html',
    'html'  => 'text/html',
    'php'   => 'text/source',
    'conf'  => 'text/source',
    'js'    => 'text/source',
    'jpe'   => 'image/jpeg',
    'jpg'   => 'image/jpeg',
    'jpeg'  => 'image/jpeg',
    'mov'   => 'video/quicktime',
    'mpe'   => 'video/mpeg',
    'mpeg'  => 'video/mpeg',
    'mpg'   => 'video/mpeg',
    'mp4'   => 'video/mpeg',
    'mpga'  => 'audio/mpeg',
    'mp2'   => 'audio/mpeg',
    'mp3'   => 'audio/mpeg',
    'pdf'   => 'application/pdf',
    'png'   => 'image/png',
    'qt'    => 'video/quicktime',
    'rtf'   => 'text/rtf',
    'svg'   => 'image/svg+xml',
    'swf'   => 'application/x-shockwave-flash',
    'tar'   => 'application/x-tar',
    'tgz'   => 'application/x-gzip',
    'tif'   => 'image/tiff',
    'tiff'  => 'image/tiff',

    'txt'   => 'text/plain',
    'doc'   => 'application/msword',
    'ppt'   => 'application/vnd.ms-powerpoint',
    'xls'   => 'application/vnd.ms-excel',
    'xml'   => 'text/xml',
    'sxw'   => 'application/vnd.sun.xml.writer',
    'sxc'   => 'application/vnd.sun.xml.calc',
    'sxi'   => 'application/vnd.sun.xml.impress',
    'sxd'   => 'application/vnd.sun.xml.draw',
    'sxm'   => 'application/vnd.sun.xml.math',

    'zip'   => 'application/zip'
);

// mime type to be used if no other type known

// ==> getfile.php
// ==> load_mimetypes()
// ==> function editeur_upload()
// ==> function forum_upload()
// ==> FileUpload => function uploadFile($IdPost, $IdTopic, $name, $size, $type, $src_file, $inline = DEFAULT_INLINE)
// ==> function getAttDisplayMode($att_type, $att_inline = 'A')
// ==> function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod)
$mimetype_default = 'application/octet-stream';

// ==> function load_mimetypes() global ne sert a rien 
// ==> ne sert a rien sauf dans une global qui ne sert a rien !!!
$mime_dspinl[$mimetype_default] = 'O';

// ==> function load_mimetypes()
// ==> function getAttDisplayMode($att_type, $att_inline = 'A')
// ==> function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod)
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



// rendu des attachements

// ==> function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod) 
$mime_renderers[ATT_DSP_PLAINTEXT] = "
                <div class=\"list-group-item flex-column align-items-start\">
                <div class=\"py-2 mb-2\"><code>\$att_name\$visible_wrn</code></div>
                <div style=\"width:100%; \">
                    <pre>\$att_contents</pre>
                </div>
                </div>";


// ==> function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod)
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


// ==> function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod) 
$mime_renderers[ATT_DSP_LINK]      = "
                <a class=\"list-group-item list-group-item-action d-flex justify-content-start align-items-center\" href=\"\$att_url\" target=\"_blank\" >\$att_icon<span title=\"" . upload_translate("Télécharg.") . " \$att_name (\$att_type - \$att_size)\" data-bs-toggle=\"tooltip\" style=\"font-size: .85rem;\" class=\"ms-2 n-ellipses\"><strong>&nbsp;\$att_name</strong></span><span class=\"badge bg-secondary ms-auto\" style=\"font-size: .75rem;\">\$compteur &nbsp;<i class=\"fa fa-lg fa-download\"></i></span><br /><span align=\"center\">\$visible_wrn</span></a>";


// ==> function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod)
$mime_renderers[ATT_DSP_IMG]       = "
                <div class=\"list-group-item list-group-item-action flex-column align-items-start\">
                <code>\$att_name</code>
                <a href=\"javascript:void(0);\" onclick=\"window.open('\$att_url','fullsizeimg','menubar=no,location=no,directories=no,status=no,copyhistory=no,height=600,width=800,toolbar=no,scrollbars=yes,resizable=yes');\"><img src=\"\$att_url\" alt=\"\$att_name\" \$img_size />\$visible_wrn </a>
                </div>";


// ==> function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod)
$mime_renderers[ATT_DSP_SWF]       = "
                <p align=\"center\">
                <object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4\,0\,2\,0\" \$img_size><param name=\"quality\" value=\"high\"><param name=\"SRC\" value=\"\$att_url\"><embed src=\"\$att_url\" quality=\"high\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" \$img_size></embed></object>\$visible_wrn
                </p>";


// ==> function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod)
$mime_renderers[ATT_DSP_VIDEO]     = "
                <div class=\"list-group-item list-group-item-action flex-column align-items-start\"><code>\$att_name</code>
                <div>
                    <video playsinline preload=\"metadata\" muted controls \$img_size >
                        <source src=\"\$att_url\" type=\"video/mp4\">
                    </video>
                </div>
                </div>";


// ==> function getAttachmentUrl($apli, $post_id, $att_id, $att_path, $att_type, $att_size, $att_inline = 0, $compteur, $visible = 0, $Mmod)
$mime_renderers[ATT_DSP_AUDIO]    = "
                <div class=\"list-group-item list-group-item-action flex-column align-items-start\"><code>\$att_name</code>
                <div>
                    <audio controls src=\"\$att_url\"></audio><br />
                </div>
                </div>";



// iconographie des extension de fichiers
$extensions = [
    'asf',
    'avi',
    'bmp',
    'box',
    'cfg',
    'cfm',
    'conf',
    'crypt',
    'css',
    'dia',
    'dir',
    'doc',
    'dot',
    'dwg',
    'excel',
    'exe',
    'filebsd',
    'filelinux',
    'fla',
    'flash',
    'gif',
    'gz',
    'gzip',
    'hlp',
    'htaccess',
    'htm',
    'html',
    'ico',
    'image',
    'img',
    'indd',
    'index',
    'ini',
    'iso',
    'java',
    'jpg',
    'js',
    'json',
    'kml',
    'lyx',
    'mdb',
    'mid',
    'mov',
    'mp3',
    'mp4',
    'mpeg',
    'mpg',
    'pdf',
    'php',
    'php3',
    'php4',
    'phps',
    'png',
    'pot',
    'ppt',
    'ps',
    'psd',
    'psp',
    'ra',
    'rar',
    'rpm',
    'rtf',
    'search',
    'sit',
    'svg',
    'swf',
    'sxc',
    'sxd',
    'sxi',
    'sys',
    'tar',
    'tgz',
    'ttf',
    'txt',
    'unknown',
    'vsd',
    'wav',
    'wbk',
    'wma',
    'wmf',
    'wmv',
    'word',
    'xls',
    'xml',
    'xsl',
    'zip'
];

// ==> function att_icon($filename) 
foreach ($extensions as $extens) {
    $att_icons[$extens] = '
        <span class="fa-stack">
            <i class="bi bi-file-earmark-fill fa-stack-2x text-body-secondary"></i>
            <span class="fa-stack-1x filetype-text small ">' . $extens . '</span>
        </span>';
}

// ==> function att_icon($filename)
$att_icon_default = '
        <span class="fa-stack">
            <i class="bi bi-file-earmark-fill fa-stack-2x text-body-secondary"></i>
            <span class="fa-stack-1x filetype-text ">?</span>
        </span>';


//  non utiliser dans l'upload         
$att_icon_multiple = '
        <span class="fa-stack">
            <i class="bi bi-file-earmark-fill fa-stack-2x text-body-secondary"></i>
            <span class="fa-stack-1x filetype-text ">...</span>
        </span>'; 

 // non utiliser dans l'upload        
$att_icon_dir = '<i class="bi bi-folder fs-3"></i>'; 
