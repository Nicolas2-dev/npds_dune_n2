<?php

namespace Modules\Upload\Support;


class UploadMineType
{

    public const LINK       = 1; // displays as link (icon)
    public const IMG        = 2; // display inline as a picture, using <img> tag
    public const HTML       = 3; // display inline as HTML, e.g. banned tags are stripped
    public const PLAINTEXT  = 4; // display inline as text, using <pre> tag
    public const SWF        = 5; // Embedded Macromedia Shockwave Flash
    public const VIDEO      = 6; // video display inline in a video html5 tag
    public const AUDIO      = 7; // audio display inline in a audio html5 tag


    // Tableaux par catégorie
    private static array $images = [
        'bmp'  => 'image/bmp',
        'gif'  => 'image/gif',
        'jpe'  => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'svg'  => 'image/svg+xml',
        'tif'  => 'image/tiff',
        'tiff' => 'image/tiff',
    ];

    private static array $videos = [
        'avi'  => 'video/x-msvideo',
        'mov'  => 'video/quicktime',
        'qt'   => 'video/quicktime',
        'mpe'  => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg'  => 'video/mpeg',
        'mp4'  => 'video/mpeg',
    ];

    private static array $audio = [
        'mp3'  => 'audio/mpeg',
        'mp2'  => 'audio/mpeg',
        'mpga' => 'audio/mpeg',
    ];

    private static array $documents = [
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
    ];

    private static array $archives = [
        'zip'  => 'application/zip',
        'tar'  => 'application/x-tar',
        'tgz'  => 'application/x-gzip',
        'gz'   => 'application/x-gzip',
    ];

    private static array $openOffice = [
        'sxw' => 'application/vnd.sun.xml.writer',
        'sxc' => 'application/vnd.sun.xml.calc',
        'sxi' => 'application/vnd.sun.xml.impress',
        'sxd' => 'application/vnd.sun.xml.draw',
        'sxm' => 'application/vnd.sun.xml.math',
    ];

    protected static string $default = 'application/octet-stream';


    // Getter générique
    public static function getMimeTypeArray(string $type): array
    {
        return match(strtolower($type)) {
            'images'     => self::$images,
            'videos'     => self::$videos,
            'audio'      => self::$audio,
            'documents'  => self::$documents,
            'archives'   => self::$archives,
            'openoffice' => self::$openOffice,
            default      => [],
        };
    }

    // Retourne le MIME type pour une extension
    public static function getMimeDefault(): string
    {
        return self::$default;
    }

    // Retourne le mode d'affichage pour un type donné
    public static function displayMimeType(?int $type = 0): array
    {
        $mimetype_default = self::getMimeDefault();
        $mime = [];

        if ($type === self::LINK) {
            $mime[$mimetype_default] = self::LINK;
        } else {
            $mime[$mimetype_default] = 'O';
        }

        return $mime;
    }

    public function displayMine(int $type): array
    {
        $mime = [];

        if ($type === self::IMG) {
            $mime['image/gif']      = self::IMG;
            $mime['image/png']      = self::IMG;
            $mime['image/x-png']    = self::IMG;
            $mime['image/jpeg']     = self::IMG;
            $mime['image/pjpeg']    = self::IMG;
            $mime['image/svg+xml']  = self::IMG;

        } elseif ($type === self::LINK) {
            $mime['image/bmp'] = self::LINK;

        } elseif ($type === self::HTML) {
            $mime['text/html'] = self::HTML;

        } elseif ($type === self::PLAINTEXT) {
            $mime['text/plain'] = self::PLAINTEXT;

        } elseif ($type === self::SWF) {
            $mime['application/x-shockwave-flash'] = self::SWF;

        } elseif ($type === self::VIDEO) {
            $mime['video/mpeg'] = self::VIDEO;

        } elseif ($type === self::AUDIO) {
            $mime['audio/mpeg'] = self::AUDIO;
        }

        return $mime;
    }

    // Retourne le MIME type pour une extension
    public static function getMimeType(string $extension): string
    {
        $allMimes = array_merge(
            self::$images,
            self::$videos,
            self::$audio,
            self::$documents,
            self::$archives,
            self::$openOffice
        );

        return $allMimes[strtolower($extension)] ?? 'application/octet-stream';
    }

}
