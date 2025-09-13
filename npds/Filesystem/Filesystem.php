<?php

namespace Npds\Filesystem;

use Npds\Filesystem\FileNotFoundException;


class Filesystem
{

    /**
     * Vérifie si un fichier ou un répertoire existe.
     *
     * @param string $path Chemin du fichier ou du répertoire
     * @return bool Retourne true si le fichier/répertoire existe, false sinon
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Obtenir le contenu d'un fichier.
     *
     * @param string $path Chemin du fichier.
     * @param bool $lock Indique si un verrouillage exclusif doit être utilisé.
     * @return string Contenu du fichier.
     *
     * @throws FileNotFoundException Si le fichier n'existe pas.
     */
    public function get(string $path, bool $lock = false): string
    {
        if ($this->isFile($path)) {
            return $lock ? $this->sharedGet($path) : file_get_contents($path);
        }

        throw new FileNotFoundException("ERROR : Le fichier n'existe pas au chemin {$path}");
    }

    /**
     * Obtenir le contenu d'un fichier avec accès partagé.
     *
     * Cette méthode ouvre le fichier en lecture seule et utilise un verrou partagé
     * pour permettre à plusieurs processus de lire simultanément.
     *
     * @param string $path Chemin du fichier.
     * @return string Contenu du fichier.
     */
    public function sharedGet(string $path): string
    {
        $contents = '';

        $handle = fopen($path, 'rb');

        if (!is_null($handle)) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);

                    $contents = fread($handle, $this->size($path) ?: 1);

                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }

        return $contents;
    }

    /**
     * Écrire le contenu dans un fichier.
     *
     * @param string $path Chemin du fichier.
     * @param string $contents Contenu à écrire.
     * @param bool $lock Indique si un verrou exclusif doit être utilisé.
     * @return int|false Nombre d'octets écrits ou false en cas d'échec.
     */
    public function put(string $path, string $contents, bool $lock = false): int|false
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * Obtenir la taille d'un fichier donné.
     *
     * @param string $path Chemin du fichier.
     * @return int Taille du fichier en octets.
     */
    public function size(string $path): int
    {
        return filesize($path);
    }

    /**
     * Retourne la date de dernière modification d'un fichier.
     *
     * @param string $path Chemin du fichier
     * @return int|false Timestamp Unix de la dernière modification ou false si le fichier n'existe pas
     */
    public function lastModified(string $path): int|false
    {
        return filemtime($path);
    }

    /**
     * Vérifie si un chemin est un répertoire.
     *
     * @param string $directory Chemin à vérifier
     * @return bool Retourne true si c'est un répertoire, false sinon
     */
    public function isDirectory(string $directory): bool
    {
        return is_dir($directory);
    }

    /**
     * Détermine si le chemin donné est un fichier.
     *
     * @param string $file Chemin à vérifier.
     * @return bool True si c'est un fichier, false sinon.
     */
    public function isFile(string $file): bool
    {
        return is_file($file);
    }

}
