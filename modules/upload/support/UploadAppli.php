<?php

namespace Modules\Upload\Support;


use Modules\Upload\Library\UploadFile;
use Modules\Upload\Support\UploadRequest;


class UploadAppli
{

    /**
     * Gère l'upload de fichiers pour un forum.
     *
     * @return string Message de succès ou d'erreur
     */
    public static function forum_upload(): string
    {
        // Note : global a virer !!!
        global $apli, $IdPost, $IdForum, $IdTopic, $pcfile, $pcfile_size, $pcfile_name, $pcfile_type, $att_count, $att_size, $total_att_count, $total_att_size;
        global $MAX_FILE_SIZE, $MAX_FILE_SIZE_TOTAL, $upload_table, $rep_upload_forum; 
        
        list($sum) = sql_fetch_row(sql_query("SELECT SUM(att_size ) 
                                            FROM $upload_table 
                                            WHERE apli = '$apli' 
                                            AND post_id = '$IdPost'"));

        // gestion du quota de place d'un post
        if (($MAX_FILE_SIZE_TOTAL - $sum) < $MAX_FILE_SIZE) {
            $MAX_FILE_SIZE = $MAX_FILE_SIZE_TOTAL - $sum;
        }

        settype($thanks_msg, 'string');

        // Récupération des valeurs de PCFILE
        global $HTTP_POST_FILES, $_FILES;

        $fic = (!empty($HTTP_POST_FILES)) ? $HTTP_POST_FILES : $_FILES;

        $pcfile_name = $fic['pcfile']['name'];
        $pcfile_type = $fic['pcfile']['type'];
        $pcfile_size = $fic['pcfile']['size'];

        $pcfile = $fic['pcfile']['tmp_name'];

        $fu = new UploadFile($rep_upload_forum, $IdForum, $apli);

        $att_count = 0;
        $att_size = 0;

        $total_att_count = 0;
        $total_att_size = 0;

        $attachments = $fu->getUploadedFiles($IdPost, $IdTopic);

        if (is_array($attachments)) {
            $att_count = $attachments['att_count'];
            $att_size = $attachments['att_size'];

            if (is_array($pcfile_name)) {
                reset($pcfile_name);

                $names = implode(', ', $pcfile_name);

                $pcfile_name = $names;
            }

            $pcfile_size = $att_size;

            $thanks_msg .= '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            ' . str_replace('{NAME}', '<strong>' . $pcfile_name . '</strong>', str_replace('{SIZE}', $pcfile_size, upload_translate("Fichier {NAME} bien reçu ({SIZE} octets transférés)"))) . '
            </div>';

            $total_att_count += $att_count;
            $total_att_size += $att_size;
        }

        return $thanks_msg;
    }

    /**
     * Gère l'upload de fichiers depuis l'éditeur.
     *
     * Cette méthode récupère le fichier envoyé via le champ 'pcfile' dans la requête,
     * initialise un objet FileUpload pour traiter l'upload, et retourne le chemin complet
     * du ou des fichiers uploadés.
     *
     * Notes :
     * - Supporte l'upload multiple.
     * - Utilise la classe UploadRequest pour récupérer les fichiers uploadés.
     * - Les variables globales utilisées :
     *   - $apli : identifiant de l'application
     *   - $rep_upload_editeur : répertoire de destination pour l'upload
     *   - $path_upload_editeur : chemin public vers le fichier uploadé
     *
     * @return string Le chemin complet du fichier uploadé, ou une chaîne vide si l'upload échoue.
     */
    public static function editeur_upload()
    {
        // Note : global a virer !!!
        global $apli, $pcfile, $pcfile_size, $pcfile_name, $pcfile_type;
        global $MAX_FILE_SIZE, $MAX_FILE_SIZE_TOTAL, $rep_upload_editeur, $path_upload_editeur;

        // Récupération des valeurs de PCFILE
        $fic = UploadRequest::all();

        $pcfile_name = $fic['pcfile']['name'];
        $pcfile_type = $fic['pcfile']['type'];
        $pcfile_size = $fic['pcfile']['size'];

        $pcfile = $fic['pcfile']['tmp_name'];

        $fu = new UploadFile($rep_upload_editeur, '', $apli);

        $attachments = $fu->getUploadedFiles('', '');

        if (is_array($attachments)) {

            //$att_count = $attachments['att_count'];
            //$att_size = $attachments['att_size'];

            if (is_array($pcfile_name)) {
                reset($pcfile_name);

                $names = implode(', ', $pcfile_name);

                $pcfile_name = $names;
            }

            return ($path_upload_editeur . $pcfile_name);
        } else {
            return '';
        }
    }

}
