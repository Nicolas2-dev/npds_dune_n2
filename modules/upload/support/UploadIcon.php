<?php

namespace Modules\Upload\Support;


// echo UploadFormIcon::getExtensions()         
// Récupère la liste complète des extensions de fichiers supportées
// Utile si tu veux générer dynamiquement des menus ou vérifier des extensions

// echo UploadFormIcon::iconsForExtensions()    
// Renvoie un tableau associatif [extension => HTML icône]
// Génère automatiquement les icônes HTML pour toutes les extensions listées

// echo UploadFormIcon::default();              
// Affiche le HTML pour un fichier inconnu
// Exemple d’utilisation : quand tu ne connais pas l’extension du fichier

// echo UploadFormIcon::multiple();             
// Affiche le HTML pour représenter plusieurs fichiers
// Utile pour les sélections multiples ou les dossiers contenant plusieurs fichiers

// echo UploadFormIcon::dir();                  
// Affiche l’icône d’un dossier
// Utilisé pour représenter un répertoire plutôt qu’un fichier


class UploadIcon
{

    /**
     * Liste des extensions de fichiers supportées pour l’icônographie
     * 
     * @var string[]
     */
    private static array $extensions = [
        'asf', 'avi', 'bmp', 'box', 'cfg', 'cfm', 'conf', 'crypt', 'css',
        'dia', 'dir', 'doc', 'dot', 'dwg', 'excel', 'exe', 'filebsd',
        'filelinux', 'fla', 'flash', 'gif', 'gz', 'gzip', 'hlp', 'htaccess',
        'htm', 'html', 'ico', 'image', 'img', 'indd', 'index', 'ini', 'iso',
        'java', 'jpg', 'js', 'json', 'kml', 'lyx', 'mdb', 'mid', 'mov', 'mp3',
        'mp4', 'mpeg', 'mpg', 'pdf', 'php', 'php3', 'php4', 'phps', 'png',
        'pot', 'ppt', 'ps', 'psd', 'psp', 'ra', 'rar', 'rpm', 'rtf', 'search',
        'sit', 'svg', 'swf', 'sxc', 'sxd', 'sxi', 'sys', 'tar', 'tgz', 'ttf',
        'txt', 'unknown', 'vsd', 'wav', 'wbk', 'wma', 'wmf', 'wmv', 'word',
        'xls', 'xml', 'xsl', 'zip'
    ];

    /**
     * Récupère la liste des extensions
     *
     * @return string[]
     */
    public static function getExtensions(): array
    {
        return self::$extensions;
    }

    /**
     * Génère l’icône par défaut pour une extension donnée
     *
     * @param string $extension
     * @return string
     */
    private static function iconForExtension(string $extension): string
    {
        return '
        <span class="fa-stack">
            <i class="bi bi-file-earmark-fill fa-stack-2x text-body-secondary"></i>
            <span class="fa-stack-1x filetype-text small ">' . $extension . '</span>
        </span>';
    }

    /**
     * Génère le tableau complet d’icônes pour un tableau d’extensions
     *
     * @return array<string,string>
     */
    public static function iconsForExtensions(): array
    {
        $icons = [];

        foreach (self::$extensions as $ext) {
            $icons[$ext] = self::iconForExtension($ext);
        }

        return $icons;
    }

    /**
     * Icône par défaut pour un fichier unique inconnu
     */
    public static function default(): string
    {
        return <<<HTML
        <span class="fa-stack">
            <i class="bi bi-file-earmark-fill fa-stack-2x text-body-secondary"></i>
            <span class="fa-stack-1x filetype-text ">?</span>
        </span>
        HTML;
    }

    /**
     * Icône pour plusieurs fichiers
     */
    public static function multiple(): string
    {
        return <<<HTML
        <span class="fa-stack">
            <i class="bi bi-file-earmark-fill fa-stack-2x text-body-secondary"></i>
            <span class="fa-stack-1x filetype-text ">...</span>
        </span>
        HTML;
    }

    /**
     * Icône pour un dossier
     */
    public static function dir(): string
    {
        return '<i class="bi bi-folder fs-3"></i>';
    }

    /**
     * Retourne l'icône correspondant à un fichier selon son extension.
     *
     * @param string $filename Le nom du fichier (ex : "document.pdf").
     * @return string Le code HTML de l'icône correspondant à l'extension.
     */
    public static function attIcon(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($extension, self::$extensions)) {
            return self::iconForExtension($extension);
        }

        return self::default();
    }

}
