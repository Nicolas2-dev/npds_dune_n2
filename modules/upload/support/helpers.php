<?php

if (! function_exists('prepare_upload_window')) {
    /**
     * Prépare l'URL ou le script JS pour ouvrir la fenêtre d'upload.
     *
     * @param string $apli
     * @param int $idPost
     * @param int $idForum
     * @param int $idTopic
     * @param string $type 'win' pour ouvrir la fenêtre, autre valeur pour juste récupérer l'URL
     * @return string
     */   
    function prepare_upload_window($apli, $IdPost, $IdForum, $IdTopic, $typeL)
    {
        if ($typeL == 'win') {
            echo "<script type=\"text/javascript\">
                //<![CDATA[
                    window.open('modules.php?ModPath=upload&ModStart=include_forum/upload_forum2&apli=$apli&IdPost=$IdPost&IdForum=$IdForum&IdTopic=$IdTopic','wtmpForum', 'menubar=no,location=no,directories=no,status=no,copyhistory=no,toolbar=no,scrollbars=yes,resizable=yes, width=575, height=480');
                //]]>
            </script>";
        } else {
            return ("'modules.php?ModPath=upload&ModStart=include_forum/upload_forum2&apli=$apli&IdPost=$IdPost&IdForum=$IdForum&IdTopic=$IdTopic','wtmpForum', 'menubar=no,location=no,directories=no,status=no,copyhistory=no,toolbar=no,scrollbars=yes,resizable=yes, width=575, height=480'");
        }
    }
}

if (! function_exists('prepare_upload_window_dev_test')) {
    /**
     * Prépare l'URL ou le script JS pour ouvrir la fenêtre d'upload.
     *
     * @param string $apli
     * @param int $idPost
     * @param int $idForum
     * @param int $idTopic
     * @param string $type 'win' pour ouvrir la fenêtre, autre valeur pour juste récupérer l'URL
     * @return string
     */
    function prepare_upload_window_dev_test(string $apli, int $idPost, int $idForum, int $idTopic, string $type): string
    {
        $url = "modules.php?ModPath=upload&ModStart=include_forum/upload_forum2"
             . "&apli={$apli}&IdPost={$idPost}&IdForum={$idForum}&IdTopic={$idTopic}";
        
        $windowOptions = "menubar=no,location=no,directories=no,status=no,"
                       . "copyhistory=no,toolbar=no,scrollbars=yes,resizable=yes,"
                       . "width=575,height=480";
        
        if ($type === 'win') {
            return "<script type=\"text/javascript\">
                window.open('{$url}', 'wtmpForum', '{$windowOptions}');
            </script>";
        }

        return "'{$url}','wtmpForum','{$windowOptions}'";
    }
}

/************************************************************************/
/* Fonction pour charger en mémoire les mimetypes                       */
/************************************************************************/
// deprecated function !!!
if (! function_exists('load_mime_types')) {
    // Note a revoir completement !!!
    function load_mime_types()
    {
        global $mimetypes, $mimetype_default, $mime_dspinl, $mime_dspfmt, $mime_renderers, $att_icons, $att_icon_default, $att_icon_multiple;

        if (defined('ATT_DSP_LINK')) {
            return;
        }

        if (file_exists('modules/upload/support/mimetypes.php')) {
            include 'modules/upload/support/mimetypes.php';
        }
    }
}