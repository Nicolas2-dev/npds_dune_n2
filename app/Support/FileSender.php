<?php

namespace App\Support;

use App\Support\Compress\GzFile;
use App\Support\Compress\ZipFile;


class FileSender
{

    /**
     * Compresse et télécharge un fichier
     */
    public static function sendFile(string $content, string $filename, string $extension): void
    {
        $msOs = get_os(); 

        if (self::isCompressionAvailable()) {
            [$arc, $filez] = self::createArchiveForOs($filename, $msOs);

            $arc->addfile($content, $filename . '.' . $extension, '');
            $arc->getArchiveData();
            $arc->filedownload($filez);
        } else {
            self::outputRawFile($content, $filename, $extension, $msOs);
        }
    }

    /**
     * Compresse et enregistre un fichier dans un répertoire
     */
    public static function sendToFile(string $content, string $directory, string $filename, string $extension): void
    {
        $msOs = get_os(); 

        if (self::isCompressionAvailable()) {
            [$arc, $filez] = self::createArchiveForOs($filename, $msOs);

            $arc->addfile($content, $filename . '.' . $extension, '');
            $arc->getArchiveData();

            $path = rtrim($directory, '/') . '/' . $filez;
            
            if (file_exists($path)) {
                unlink($path);
            }

            $arc->filewrite($path, null);
        } else {
            self::outputRawFile($content, $filename, $extension, $msOs);
        }
    }

    /**
     * Crée une archive adaptée à l'OS du client
     */
    protected static function createArchiveForOs(string $filename, bool $msOs): array
    {
        if ($msOs) {
            $arc = new ZipFile();
            $filez = $filename . '.zip';
        } else {
            $arc = new GzFile();
            $filez = $filename . '.gz';
        }

        return [$arc, $filez];
    }

    /**
     * Envoie un flux brut au navigateur
     */
    protected static function outputRawFile(string $content, string $filename, string $extension, bool $msOs): void
    {
        $contentType = $msOs ? 'application/octetstream' : 'application/octet-stream';

        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $filename . '.' . $extension . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $content;
    }

    /**
     * Vérifie si la compression est disponible
     */
    protected static function isCompressionAvailable(): bool
    {
        return class_exists(\App\Support\Compress\Archive::class) 
               && function_exists('gzcompress') 
               && extension_loaded('zlib');
    }
}
