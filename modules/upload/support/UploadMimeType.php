<?php

namespace Modules\Upload\Support;


class UploadMimeType
{

    /**
     * Valeur MIME par défaut si l'extension n'est pas reconnue.
     */
    protected const DEFAULT_MIME = 'application/octet-stream';

    /**
     * Catégories de fichiers avec leurs extensions et MIME types.
     *
     * @var array<string, array<string, string>>
     */
    private static array $categories = [
        'images' => [
            'bmp'  => 'image/bmp',
            'gif'  => 'image/gif',
            'jpe'  => 'image/jpeg',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'svg'  => 'image/svg+xml',
            'tif'  => 'image/tiff',
            'tiff' => 'image/tiff',
        ],
        'videos' => [
            'avi'  => 'video/x-msvideo',
            'mov'  => 'video/quicktime',
            'qt'   => 'video/quicktime',
            'mpe'  => 'video/mpeg',
            'mpeg' => 'video/mpeg',
            'mpg'  => 'video/mpeg',
            'mp4'  => 'video/mpeg',
        ],
        'audio' => [
            'mp3'  => 'audio/mpeg',
            'mp2'  => 'audio/mpeg',
            'mpga' => 'audio/mpeg',
        ],
        'documents' => [
            'txt'  => 'text/plain',
            'bat'  => 'text/plain',
            'bak'  => 'text/plain',
            'htm'  => 'text/html',
            'html' => 'text/html',
            'php'  => 'text/source',
            'conf' => 'text/source',
            'js'   => 'text/source',
            'rtf'  => 'text/rtf',
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'xls'  => 'application/vnd.ms-excel',
        ],
        'archives' => [
            'zip' => 'application/zip',
            'tar' => 'application/x-tar',
            'tgz' => 'application/x-gzip',
            'gz'  => 'application/x-gzip',
        ],
        'openoffice' => [
            'sxw' => 'application/vnd.sun.xml.writer',
            'sxc' => 'application/vnd.sun.xml.calc',
            'sxi' => 'application/vnd.sun.xml.impress',
            'sxd' => 'application/vnd.sun.xml.draw',
            'sxm' => 'application/vnd.sun.xml.math',
        ],
    ];

    /**
     * Tableau fusionné de toutes les extensions vers leurs MIME types.
     *
     * @var array<string, string>|null
     */
    private static ?array $allMimes = null;

    /**
     * Retourne toutes les extensions et leurs MIME types.
     *
     * @return array<string, string> Tableau extension => MIME
     */
    public static function getAllMimes(): array
    {
        if (self::$allMimes === null) {
            self::$allMimes = array_merge(...array_values(self::$categories));
        }
        return self::$allMimes;
    }

    /**
     * Retourne le type MIME correspondant à une extension donnée.
     *
     * @param string $ext Extension du fichier
     * @return string MIME type correspondant ou DEFAULT_MIME si inconnu
     */
    public static function getMimeByExtension(string $ext): string
    {
        return self::getAllMimes()[strtolower($ext)] ?? self::DEFAULT_MIME;
    }

    /**
     * Retourne un tableau de MIME types selon le mode d'upload.
     *
     * @param UploadMode $mode Mode d'upload
     * @return array<string, string> Tableau MIME => MIME
     */
    public static function displayMimeByMode(UploadMode $mode): array
    {
        $map = [
            UploadMode::IMG       => self::$categories['images'],
            UploadMode::LINK      => ['image/bmp' => 'image/bmp'],
            UploadMode::HTML      => ['text/html' => 'text/html'],
            UploadMode::PLAINTEXT => ['text/plain' => 'text/plain'],
            UploadMode::SWF       => ['application/x-shockwave-flash' => 'application/x-shockwave-flash'],
            UploadMode::VIDEO     => array_combine(array_values(self::$categories['videos']), array_values(self::$categories['videos'])),
            UploadMode::AUDIO     => array_combine(array_values(self::$categories['audio']), array_values(self::$categories['audio'])),
        ];

        return $map[$mode] ?? [self::DEFAULT_MIME => self::DEFAULT_MIME];
    }

    /**
     * Retourne le MIME par défaut utilisé pour les fichiers inconnus
     *
     * @return string
     */
    public static function getDefaultMime(): string
    {
        return self::DEFAULT_MIME;
    }

    /**
     * Retourne toutes les catégories de fichiers avec leurs extensions et MIME types.
     *
     * @return array<string, array<string, string>> Tableau catégorie => (extension => MIME)
     */
    public static function getCategories(): array
    {
        return self::$categories;
    }

}

