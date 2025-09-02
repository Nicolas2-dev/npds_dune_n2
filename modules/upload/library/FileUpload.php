<?php

namespace Modules\Upload\Library;

use App\Library\Log\Log;
use App\Library\Http\Request;
use Modules\Upload\Support\UploadAttachment;


class FileUpload
{

    /**
     * Code d'erreur : aucun fichier fourni.
     */
    public const NO_FILE = -1;

    /**
     * Code d'erreur : le fichier est trop volumineux.
     */
    public const FILE_TOO_BIG = -2;

    /**
     * Code d'erreur : type de fichier invalide.
     */
    public const INVALID_FILE_TYPE = -3;

    /**
     * Code d'erreur : erreur lors de l'opération sur la base de données.
     */
    public const DB_ERROR = -4;

    /**
     * Code d'erreur : impossible de copier le fichier.
     */
    public const COPY_ERROR = -5;

    /**
     * Code d'erreur : erreur générale sur le fichier.
     */
    public const ERR_FILE = -6;

    /**
     * Code d'erreur : le fichier est vide.
     */
    public const FILE_EMPTY = -7;

    /**
     * Code d'erreur : argument fourni incorrect ou manquant.
     */
    public const ERR_ARG = -8;

    /**
     * Paramètre par défaut pour le mode inline (valeur '1').
     */
    public const DEFAULT_INLINE = '1';

    /**
     * Masque de permission par défaut pour les fichiers/dossiers (octal 0766).
     */
    public const U_MASK = 0766;

    /*
     * @var int Current error code 
     */
    public int $errno = 0;

    /** 
     * @var string Directory where files will be uploaded 
     */
    public string $uploadDir;

    /** 
     * @var string Forum ID 
     */
    public string $idForum;

    /** 
     * @var string Application name 
     */
    public string $apli;

    /** 
     * @var string Halt behavior on error ('no', 'report', ...) 
     */
    public string $haltOnError = 'report';


    /**
     * FileUpload constructor.
     *
     * @param string $uploadDir
     * @param string $idForum
     * @param string $apli
     */
    public function __construct(string $uploadDir, string $idForum, string $apli, string $haltOnError = 'no')
    {
        $this->uploadDir    = $uploadDir;
        $this->idForum      = $idForum;
        $this->apli         = $apli;
        $this->haltOnError  = $haltOnError;
    }

    /**
     * Définit le comportement de la classe en cas d'erreur.
     *
     * Les valeurs possibles peuvent être :
     * - 'no'      : ne rien faire
     * - 'report'  : signaler l'erreur
     *
     * @param string $mode Le mode de gestion des erreurs.
     * @return void
     */
    public function setHaltOnError(string $mode): void
    {
        $this->haltOnError = $mode;
    }

    /**
     * Handle errors based on $errno and $haltOnError
     *
     * @param string $msg Optional custom message
     * @return void
     */
    public function halt(string $msg = ''): void
    {
        if ($this->haltOnError == 'no') {
            return;
        }

        switch ($this->errno) {

            case self::FILE_TOO_BIG:
                $reason = upload_translate('La taille de ce fichier excède la taille maximum autorisée') . ' !</div>';
                break;

            case self::INVALID_FILE_TYPE:
                $reason = upload_translate('Ce type de fichier n\'est pas autorisé') . ' !</div>';
                break;

            default;
                $reason = sprintf(upload_translate('Le code erreur est : %s'), $this->errno);
                break;
        }

        /*Note : je ne trouve pas quand et ou cette variable défini ci dessus peut etre changé donc ne comprend pas les conditions ci dessous ?*/

        if ($this->haltOnError == 'report') {
            printf('<div class="alert alert-danger m-3 alert-dismissible fade show" role="alert"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button><h4 class="alert-heading">' . upload_translate('Attention') . '</h4> %s<br /><p class="mt-2 text-center"> %s </p>', $msg, '<strong>' . $reason . '</strong>');
        } else {
            printf('<div class="alert alert-danger m-3" role="alert"> %s %s<br /><p class="mt-2 text-center"> %s </p></span>', '<h4 class="alert-heading">File management</h4>', $msg, '<strong>' . $reason . '</strong>');
        }

        if ($this->haltOnError != 'report') {
            die('<div class="alert alert-danger m-3" role="alert">' . upload_translate('Session terminée.') . '</div>');
        }
    }

    /**
     * Copy uploaded file to destination and insert DB entry.
     *
     * @param int $idPost
     * @param int $idTopic
     * @param string $name
     * @param int $size
     * @param string $type
     * @param string $src_file
     * @param string $inline
     * @return bool True if successful, false otherwise
     */
    public function uploadFile(int $idPost, int $idTopic, string $name, int $size, string $type, string $srcFile, string $inline = self::DEFAULT_INLINE): bool
    {
        global $MAX_FILE_SIZE, $mimetypes, $mimetype_default, $insert_base;

        $this->errno = 0;

        // Check temporary file
        if (empty($srcFile) || (strcasecmp($srcFile, 'none') == 0)) {
            $this->errno = self::NO_FILE;

            return false;
        }

        // Check size
        if ($size == 0) {
            $this->errno = self::FILE_EMPTY;

            return false;
        } else {
            $fsize = filesize($srcFile);
        }

        if ($size != $fsize) {
            $this->errno = self::ERR_FILE;

            return false;
        }

        if ($size > $MAX_FILE_SIZE) {
            $this->errno = self::FILE_TOO_BIG;

            return false;
        }

        // Check name
        if (empty($name)) {
            $this->errno = self::NO_FILE;

            return false;
        }

        $name = preg_replace('#[/\\\:\*\?"<>|]#i', '_', rawurldecode($name));

        // Check type and extension
        load_mime_types();

        $suffix = strtoLower(substr(strrchr($name, '.'), 1));

        if (isset($mimetypes[$suffix])) {
            $type = $mimetypes[$suffix];
        } elseif (empty($type) || ($type == 'application/octet-stream')) {
            $type = $mimetype_default;
        }

        if (! $this->isAllowedFile($name, $type)) {
            $this->errno = self::INVALID_FILE_TYPE;

            return false;
        }

        // Find the path to upload directory
        global $DOCUMENTROOT;
        $rep = $DOCUMENTROOT;

        settype($log_filename, 'string');

        if ($insert_base == true) {

            // insert attachment reference in database
            $id = UploadAttachment::insertAttachment($this->apli, $idPost, $idTopic, $this->idForum, $name, $this->uploadDir, $inline, $size, $type);

            if ($id <= 0) {
                $this->errno = self::DB_ERROR;
                return false;
            }

            // copy temporary file to the upload directory
            $dest_file = $rep . $this->uploadDir . "$id." . $this->apli . ".$name";

            $copyfunc = (function_exists('move_uploaded_file')) ? 'move_uploaded_file' : 'copy';

            if (! $copyfunc($srcFile, $dest_file)) {
                UploadAttachment::deleteAttachment($this->apli, $idPost, $rep . $this->uploadDir, $id, $name);

                $this->errno = self::COPY_ERROR;

                return false;
            }

            @chmod($dest_file, 0766);

            $log_filename = $dest_file;
        } else {
            if ($this->apli == 'minisite') {

                // copy temporary file to the upload directory
                global $rep_upload_minisite;

                $copyfunc = (function_exists('move_uploaded_file')) ? 'move_uploaded_file' : 'copy';

                if (! $copyfunc($srcFile, $rep . $rep_upload_minisite . $name)) {
                    $this->errno = self::COPY_ERROR;

                    return false;
                }

                @chmod($rep . $rep_upload_minisite . $name, 0766);

                $log_filename = $rep . $rep_upload_minisite . $name;
            } elseif ($this->apli == 'editeur') {

                // copy temporary file to the upload directory

                global $rep_upload_editeur;

                $copyfunc = (function_exists('move_uploaded_file')) ? 'move_uploaded_file' : 'copy';

                if (! $copyfunc($srcFile, $rep . $rep_upload_editeur . $name)) {
                    $this->errno = self::COPY_ERROR;

                    return false;
                }

                @chmod($rep . $rep_upload_editeur . $name, 0766);

                $log_filename = $rep . $rep_upload_editeur . $name;
            } else {
                return false;
            }
        }

        Log::ecrireLog('security', 'Upload File(s) : ' . Request::getip(), $log_filename);

        return true;
    }

    /**
     * Get uploaded files for a post/topic
     *
     * @param int $idPost
     * @param int $idTopic
     * @return array|false Array with 'att_size' and 'att_count' or false
     */
    public function getUploadedFiles(int $idPost, int $idTopic): array|false
    {
        global $pcfile, $pcfile_size, $pcfile_name, $pcfile_type;

        $this->errno = 0;

        $att_size = 0;
        $att_count = 0;

        if (is_string($pcfile) && !empty($pcfile) && !empty($pcfile_name)) {
            if ($pcfile == 'none') {
                $errmsg = sprintf(upload_translate('Erreur de téléchargement du fichier %s (%s) - Le fichier n\'a pas été sauvé'), $pcfile_name, $pcfile_type);

                $this->errno = self::NO_FILE;

                $this->halt($errmsg);
            } elseif ($this->uploadFile($idPost, $idTopic, $pcfile_name, $pcfile_size, $pcfile_type, $pcfile, self::DEFAULT_INLINE)) {
                $att_size = $pcfile_size;
                $att_count = 1;
            } else {
                $errmsg = sprintf(upload_translate('Erreur de téléchargement du fichier %s (%s) - Le fichier n\'a pas été sauvé'), $pcfile_name, $pcfile_type);

                $this->halt($errmsg);
            }
        } elseif (is_array($pcfile)) {
            $nfiles = count($pcfile);

            for ($i = 0; $i < $nfiles; $i++) {
                if (!empty($pcfile[$i]) && (strtolower($pcfile[$i]) != 'none')) {

                    if ($this->uploadFile($idPost, $idTopic, $pcfile_name[$i], $pcfile_size[$i], $pcfile_type[$i], $pcfile[$i], self::DEFAULT_INLINE)) {
                        $att_size += $pcfile_size[$i];

                        $att_count++;
                    } else {
                        $errmsg = sprintf(upload_translate('Erreur de téléchargement du fichier %s (%s) - Le fichier n\'a pas été sauvé'), $pcfile_name[$i], $pcfile_type[$i]);

                        $this->halt($errmsg);
                    }
                }
            }
        } else {
            $this->errno = self::NO_FILE;

            return false;
        }

        if ($att_size > 0) {
            $att['att_size'] = $att_size;
            $att['att_count'] = $att_count;

            return $att;
        } else {
            return false;
        }
    }

    /**
     * Check if file is allowed based on extension or mime-type.
     *
     * @param string $filename
     * @param string $mimetype
     * @return bool
     */
    public function isAllowedFile(string $filename, string $mimetype): bool
    {
        global $bn_allowed_extensions, $bn_allowed_mimetypes, $bn_banned_extensions, $bn_banned_mimetypes;

        // First check allowed extensions
        $ext = strtolower(strrchr($filename, '.'));

        if (!empty($bn_allowed_extensions)) {
            $allowed_extensions = explode(' ', $bn_allowed_extensions);

            if (is_array($allowed_extensions)) {
                $found = false;

                foreach ($allowed_extensions as $goodext) {
                    if ($ext == $goodext) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    return false;
                }
            }
        }

        // Now deny banned extension
        if (!empty($bn_banned_extensions)) {
            $banned_extensions = explode(' ', $bn_banned_extensions);

            if (is_array($banned_extensions)) {
                foreach ($banned_extensions as $badext) {
                    if ($ext == $badext) {
                        return false;
                    }
                }
            }
        }

        // Now check mime-type
        list($type, $subtype) = explode('/', $mimetype);

        // check allowed mime-types
        if (!empty($bn_allowed_mimetypes)) {
            $allowed_mimetypes = explode(' ', $bn_allowed_mimetypes);

            if (is_array($allowed_mimetypes)) {
                $found = false;

                foreach ($allowed_mimetypes as $mt) {
                    list($good_type, $good_subtype) = explode('/', $mt);

                    if ($type == $good_type) {
                        if (($good_subtype == '*') || ($subtype == $good_subtype)) {
                            $found = true;
                            break;
                        }
                    }
                }

                if (!$found) {
                    return false;
                }
            }
        }

        // check denied mime-types
        if (!empty($bn_banned_mimetypes)) {
            $banned_mimetypes = explode(' ', $bn_banned_mimetypes);

            if (is_array($banned_mimetypes)) {
                foreach ($banned_mimetypes as $mt) {
                    list($bad_type, $bad_subtype) = explode('/', $mt);

                    if ($type == $bad_type) {
                        if (($bad_subtype == '*') || ($subtype == $bad_subtype)) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }
}
