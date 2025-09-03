<?php

namespace Shared\FileSend\Compress;


/**
 * Classe de base pour la gestion des archives
 */
class Archive
{

    /**
     * Détermine si l'écriture d'une archive doit écraser un fichier existant
     * @var bool
     */
    protected bool $overwrite = false;

    /**
     * Permissions par défaut pour les fichiers créés
     * @var int
     */
    protected int $defaultPerms = 0644;

    /**
     * Répertoire de travail pour les opérations sur les fichiers
     * @var string
     */
    protected string $cwd = './';

    /**
     * Liste des erreurs rencontrées lors des opérations
     * @var array
     */
    protected array $errors = [];

    /**
     * Détermine si la récursion est activée
     *
     * @var int
     */
    protected int $recurseSd = 0;

    
    /**
     * Constructeur
     * 
     * @param array $flags Tableau associatif de paramètres :
     *                     - 'overwrite' bool : écrasement des fichiers existants
     *                     - 'defaultperms' int : permissions par défaut pour les fichiers
     */
    public function __construct(array $flags = [])
    {
        if (isset($flags['overwrite'])) {
            $this->overwrite = (bool) $flags['overwrite'];
        }

        if (isset($flags['defaultperms'])) {
            $this->defaultPerms = (int) $flags['defaultperms'];
        }
    }

    /**
     * Ajoute des répertoires et fichiers à l'archive
     *
     * @param array $dirList Liste des répertoires ou fichiers à ajouter
     */
    public function addDirectories(array $dirList): void
    {
        $pwd = getcwd();

        if (property_exists($this, 'cwd') && isset($this->cwd)) {
            @chdir($this->cwd);
        }

        $fileList = [];

        foreach ($dirList as $current) {
            if (@is_dir($current)) {
                $temp = $this->parseDirectories($current);
                $fileList = array_merge($fileList, $temp);
            } elseif (@file_exists($current)) {
                $fileList[] = $current;
            }
        }

        if ($pwd !== false) {
            @chdir($pwd);
        }

        $this->addFiles($fileList);
    }

    /**
     * Parcourt récursivement un répertoire pour récupérer tous les fichiers
     *
     * @param string $dirname Chemin du répertoire
     * @return array Liste des fichiers trouvés
     */
    protected function parseDirectories(string $dirname): array
    {
        $fileList = [];
        $dir = @opendir($dirname);

        if ($dir === false) {
            return $fileList;
        }

        while (($file = readdir($dir)) !== false) {
            if (in_array($file, ['.', '..', 'default.html', 'index.html'])) {
                continue;
            }

            $fullPath = $dirname . DIRECTORY_SEPARATOR . $file;

            if (@is_dir($fullPath)) {
                if (property_exists($this, 'recurseSd') && $this->recurseSd === 1) {
                    $temp = $this->parseDirectories($fullPath);

                    $fileList = array_merge($fileList, $temp);
                }
            } elseif (@file_exists($fullPath)) {
                $fileList[] = $fullPath;
            }
        }

        @closedir($dir);

        return $fileList;
    }

    /**
     * Écrit les données de l'archive dans un fichier
     *
     * @param string $filename Chemin du fichier cible
     * @param int|null $perms Permissions à appliquer (optionnel)
     * @return bool Succès ou échec de l'opération
     */
    public function fileWrite(string $filename, ?int $perms = null): bool
    {
        if (!$this->overwrite && @file_exists($filename)) {
            return $this->error('Le fichier $filename existe déjà.');
        }

        if (@file_exists($filename)) {
            @unlink($filename);
        }

        $fp = @fopen($filename, 'wb');

        if ($fp === false) {
            return $this->error('Impossible d\'ouvrir le fichier ' . $filename . ' en écriture.');
        }

        $data = $this->getArchiveData();

        if (!fwrite($fp, $data)) {
            @fclose($fp);

            return $this->error('Impossible d\'écrire les données dans le fichier ' . $filename . '.');
        }

        @fclose($fp);

        $perms = $perms ?? $this->defaultPerms;
        @chmod($filename, $perms);

        return true;
    }

    /**
     * Extrait le contenu d'un fichier d'archive
     *
     * @param string $filename Chemin du fichier d'archive
     * @return array|bool Tableau des fichiers extraits ou false en cas d'erreur
     */
    public function extractFile(string $filename): array|bool
    {
        $fp = @fopen($filename, 'rb');

        if ($fp === false) {
            $this->error('Impossible d\'ouvrir le fichier ' . $filename . '.');

            return false;
        }

        $fileSize = filesize($filename);

        if ($fileSize === false || $fileSize === 0) {
            @fclose($fp);

            $this->error('Fichier $filename vide.');

            return false;
        }

        $data = fread($fp, $fileSize);
        @fclose($fp);

        if ($data === false) {
            $this->error('Impossible de lire le fichier ' . $filename . '.');

            return false;
        }

        return $this->extract($data);
    }

    /**
     * Enregistre une erreur et retourne false
     *
     * @param string $error Message d'erreur
     * @return bool Toujours false
     */
    protected function error(string $error): bool
    {
        $this->errors[] = $error;

        return false;
    }

    /**
     * Retourne la liste des erreurs rencontrées
     *
     * @return array Liste des messages d'erreur
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne les données de l'archive (à implémenter dans les classes filles)
     *
     * @return string Données brutes de l'archive
     */
    protected function getArchiveData(): string
    {
        return '';
    }

    /**
     * Extrait les données d'une archive (à implémenter dans les classes filles)
     *
     * @param string $data Données brutes de l'archive
     * @return array|bool Fichiers extraits ou false
     */
    protected function extract(string $data): array|bool
    {
        return false;
    }

    /**
     * Ajoute une liste de fichiers à l'archive (à implémenter dans les classes filles)
     *
     * @param array $fileList Liste de fichiers
     */
    protected function addFiles(array $fileList): void
    {
        // À implémenter dans les classes filles
    }
}
