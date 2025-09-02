<?php

use Modules\Upload\Support\UploadAppli;

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

if (!stristr($_SERVER['PHP_SELF'], 'modules.php')) {
    die();
}

/*****************************************************/
/* Include et definition                             */
/*****************************************************/

include_once 'modules/upload/language/'. $language .'/upload.lang-'. $language .'.php';
include_once 'modules/upload/config/upload.conf.editeur.php';

/*****************************************************/
/* Entete                                            */
/*****************************************************/
$Titlesitename = upload_translate('Télécharg.');

include 'storage/meta/meta.php';

if ($url_upload_css) {
    $url_upload_cssX = str_replace('style.css', "$language-style.css", $url_upload_css);

    if (is_readable($url_upload . $url_upload_cssX)) {
        $url_upload_css = $url_upload_cssX;
    }

    print("<link href=\"" . $url_upload . $url_upload_css . "\" title=\"default\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />\n");
}

echo "</head>\n";

if (isset($actiontype)) {

    switch ($actiontype) {

        case 'upload':
            $ret = UploadAppli::editeur_upload();

            $js = '';

            if ($ret != '') {
                $suffix = strtoLower(substr(strrchr($ret, '.'), 1));

                if ($suffix == 'gif' or  $suffix == 'jpg' or  $suffix == 'jpeg' or $suffix == 'png') {
                    $js .= "parent.tinymce.activeEditor.selection.setContent('<img class=\"img-fluid\" src=\"$ret\" alt=" . basename($ret) . " loading=\"lazy\" />');";
                } else {
                    $js .= "parent.tinymce.activeEditor.selection.setContent('<a href=\"$ret\" target=\"_blank\">" . basename($ret) . "</a>');";
                }
            }

            echo "<script type=\"text/javascript\">
                    //<![CDATA[
                        " . $js . "
                        top.tinymce.activeEditor.windowManager.close();
                    //]]>
                </script>";

            die();
            break;
    }
}

echo '<body topmargin="3" leftmargin="3" rightmargin="3">
        <div class="card card-body mx-2 mt-3">
            <form method="post" action="' . $_SERVER['PHP_SELF'] . '" enctype="multipart/form-data" name="formEdit">
                <input type="hidden" name="ModPath" value="' . $ModPath . '" />
                <input type="hidden" name="ModStart" value="' . $ModStart . '" />
                <input type="hidden" name="apli" value="' . $apli . '" />';

if (isset($groupe)) {
    echo '<input type="hidden" name="groupe" value="' . $groupe . '" />';
}

echo '<div class="mb-3 row">
                <input type="hidden" name="actiontype" value="upload" />
                <label class="form-label">' . upload_translate('Fichier') . '</label>
                <input class="form-control" name="pcfile" type="file" id="pcfile" value="" />
                </div>
                <div class="mb-3 row">
                <input type="submit" class="btn btn-primary btn-sm" name="insert" value="' . upload_translate('Joindre') . '" />
                </div>
            </form>
        </div>
    </body>
    </html>';
