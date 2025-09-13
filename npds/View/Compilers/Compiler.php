<?php

namespace Npds\View\Compilers;

use Npds\Filesystem\Filesystem;


abstract class Compiler 
{

    /**
     * Instance du gestionnaire de fichiers.
     *
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * Chemin vers le dossier de cache des fichiers compilés.
     *
     * @var string
     */
    protected string $cachePath;


    /**
     * Constructeur.
     *
     * @param Filesystem $files     Instance du gestionnaire de fichiers.
     * @param string     $cachePath Chemin vers le dossier de cache.
     */
    public function __construct(Filesystem $files, string $cachePath)
    {
        $this->files = $files;

        $this->cachePath = $cachePath;
    }

    /**
     * Retourne le chemin du fichier compilé correspondant à une vue donnée.
     *
     * @param string $path Chemin de la vue source.
     * @return string Chemin du fichier compilé.
     */
    public function getCompiledPath(string $path): string
    {
        return $this->cachePath .DS .sha1($path) .'.php';
    }

    /**
     * Détermine si la vue est obsolète et doit être recompilée.
     *
     * @param string $path Chemin de la vue source.
     * @return bool True si la vue est expirée, false sinon.
     */
    public function isExpired(string $path): bool
    {
        $compiled = $this->getCompiledPath($path);

        // Si le chemin de cache est nul ou si le fichier compilé n'existe pas, la vue est considérée comme expirée.
        if (is_null($this->cachePath) || ! $this->files->exists($compiled)) {
            return true;
        }

        $lastModified = $this->files->lastModified($path);

        // Si la vue source a été modifiée après le fichier compilé, la vue est expirée.
        if ($lastModified >= $this->files->lastModified($compiled)) {
            return true;
        }

        return false;
    }

}
