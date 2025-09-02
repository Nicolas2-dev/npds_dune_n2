<?php

namespace Modules\Upload\Library;

use Modules\Upload\Support\UploadRequest;


class Upload
{

    /**
     * Taille maximale d'upload en octets.
     *
     * @var int
     */
    public int $maxupload_size;

    /**
     * Message d'erreur en cas de problème.
     *
     * @var string
     */
    public string $errors = '';

    /**
     * Indique si un fichier a été posté.
     *
     * @var bool
     */
    public bool $isPosted = false;

    /**
     * Contenu de $_FILES.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $uploadedFiles = [];


    /**
     * Constructeur de la classe Upload.
     */
    public function __construct()
    {
        // Récupère tous les fichiers postés via UploadRequest
        $this->uploadedFiles = UploadRequest::all();

        // Détermine si des fichiers ont été postés
        $this->isPosted = !empty($this->uploadedFiles);
    }

    /**
     * Sauvegarde un fichier uploadé.
     *
     * @param string $filename Nom du fichier de destination
     * @param string $directory Répertoire de destination
     * @param string $field Nom du champ input file
     * @param bool $overwrite Indique si le fichier existant doit être écrasé
     * @param int $mode Permissions du fichier (par défaut 0766)
     * @return bool Vrai si la sauvegarde a réussi, faux sinon
     */
    public function saveAs(string $filename, string $directory, string $field, bool $overwrite, int $mode = 0766): bool
    {
        if ($this->isPosted) {
            if ($this->uploadedFiles[$field]['size'] < $this->maxupload_size && $this->uploadedFiles[$field]['size'] > 0) {
                $noerrors = true;

                $tempName = $this->uploadedFiles[$field]['tmp_name'];

                $all      = $directory . $filename;

                if (file_exists($all)) {
                    if ($overwrite) {

                        @unlink($all) || $noerrors = false;

                        $this->errors  = upload_translate('Erreur de téléchargement du fichier - fichier non sauvegardé.');

                        @move_uploaded_file($tempName, $all) || $noerrors = false;

                        $this->errors .= upload_translate('Erreur de téléchargement du fichier - fichier non sauvegardé.');

                        @chmod($all, $mode);
                    }
                } else {
                    @move_uploaded_file($tempName, $all) || $noerrors = false;

                    $this->errors  = upload_translate('Erreur de téléchargement du fichier - fichier non sauvegardé.');

                    @chmod($all, $mode);
                }

                return $noerrors;

            } elseif ($this->uploadedFiles[$field]['size'] > $this->maxupload_size) {
                $this->errors = upload_translate('La taille de ce fichier excède la taille maximum autorisée') . " => " . number_format(($this->maxupload_size / 1024), 2) . " Kbs";

                return false;

            } elseif ($this->uploadedFiles[$field]['size'] == 0) {
                $this->errors = upload_translate('Erreur de téléchargement du fichier - fichier non sauvegardé.');

                return false;
            }
        }

        return false;
    }

    /**
     * Retourne le nom du fichier uploadé pour un champ donné.
     *
     * @param string $field
     * @return string
     */
    public function getFilename(string $field): string
    {
        return $this->uploadedFiles[$field]['name'];
    }

    /**
     * Retourne le type MIME du fichier uploadé pour un champ donné.
     *
     * @param string $field
     * @return string
     */
    public function getFileMimeType(string $field): string
    {
        return $this->uploadedFiles[$field]['type'];
    }

    /**
     * Retourne la taille du fichier uploadé pour un champ donné.
     *
     * @param string $field
     * @return int
     */
    public function getFileSize(string $field): int
    {
        return $this->uploadedFiles[$field]['size'];
    }

}
