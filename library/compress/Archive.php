<?php

/**
 * Classe de base pour les archives
 */
class Archive
{
    protected bool $overwrite = false;

    protected int $defaultPerms = 0644;

    protected string $cwd = './';

    protected array $errors = [];


    public function __construct(array $flags = [])
    {
        if (isset($flags['overwrite'])) {
            $this->overwrite = (bool)$flags['overwrite'];
        }

        if (isset($flags['defaultperms'])) {
            $this->defaultPerms = (int)$flags['defaultperms'];
        }
    }

    /**
     * Ajoute des répertoires à l'archive
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
     * Parse récursivement les répertoires
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
     * Écrit l'archive dans un fichier
     */
    public function fileWrite(string $filename, ?int $perms = null): bool
    {
        if (!$this->overwrite && @file_exists($filename)) {
            return $this->error("Le fichier $filename existe déjà.");
        }

        if (@file_exists($filename)) {
            @unlink($filename);
        }

        $fp = @fopen($filename, 'wb');
        if ($fp === false) {
            return $this->error("Impossible d'ouvrir le fichier $filename en écriture.");
        }

        $data = $this->getArchiveData();
        if (!fwrite($fp, $data)) {
            @fclose($fp);
            return $this->error("Impossible d'écrire les données dans le fichier $filename.");
        }
        
        @fclose($fp);
        
        $perms = $perms ?? $this->defaultPerms;
        @chmod($filename, $perms);
        
        return true;
    }

    /**
     * Extrait un fichier d'archive
     */
    public function extractFile(string $filename): array|bool
    {
        $fp = @fopen($filename, 'rb');
        if ($fp === false) {
            $this->error("Impossible d'ouvrir le fichier $filename.");
            return false;
        }

        $fileSize = filesize($filename);
        if ($fileSize === false || $fileSize === 0) {
            @fclose($fp);
            $this->error("Fichier $filename vide.");
            return false;
        }

        $data = fread($fp, $fileSize);
        @fclose($fp);
        
        if ($data === false) {
            $this->error("Impossible de lire le fichier $filename.");
            return false;
        }

        return $this->extract($data);
    }

    /**
     * Gère les erreurs
     */
    protected function error(string $error): bool
    {
        $this->errors[] = $error;
        return false;
    }

    /**
     * Retourne les erreurs
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Méthodes abstraites à implémenter dans les classes filles
     */
    protected function getArchiveData(): string
    {
        return '';
    }

    protected function extract(string $data): array|bool
    {
        return false;
    }

    protected function addFiles(array $fileList): void
    {
        // À implémenter dans les classes filles
    }
}
