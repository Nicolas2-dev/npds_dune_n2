<?php

namespace Shared\FileSend\Compress;

use Shared\FileSend\Compress\Archive;


/**
 * Classe pour la gestion des fichiers ZIP
 * Hérite de la classe Archive.
 */
class ZipFile extends Archive
{

    /** 
     * @var string Répertoire courant pour l'ajout de fichiers 
     */
    protected string $cwd = './';

    /** 
     * @var string Commentaire de l'archive ZIP 
     */
    protected string $comment = '';

    /** 
     * @var int Niveau de compression (0 à 9) 
     */
    protected int $level = 9;

    /** 
     * @var int Position actuelle dans l'archive 
     */
    protected int $offset = 0;

    /** 
     * @var int Activer la récursivité pour sous-dossiers 
     */
    protected int $recurseSd = 1;

    /**  
     * @var int Indique si le chemin doit être conservé
     */
    protected int $storePath = 1;

    /**  
     * @var int Temps de remplacement pour les fichiers
     */
    protected int $replaceTime = 0;

    /** 
     * @var array Stocke les en-têtes centraux des fichiers 
     */
    protected array $central = [];

    /** 
     * @var array Stocke les données compressées de l'archive 
     */
    protected array $zipData = [];


    /**
     * Constructeur
     *
     * @param string $cwd Répertoire de travail
     * @param array $flags Options : 'time', 'recursesd', 'storepath', 'level', 'comment'
     */
    public function __construct(string $cwd = './', array $flags = [])
    {
        parent::__construct($flags);

        $this->cwd = $cwd;

        if (isset($flags['time'])) {
            $this->replaceTime = (int)$flags['time'];
        }

        if (isset($flags['recursesd'])) {
            $this->recurseSd = (int)$flags['recursesd'];
        }

        if (isset($flags['storepath'])) {
            $this->storePath = (int)$flags['storepath'];
        }

        if (isset($flags['level'])) {
            $this->level = max(0, min(9, (int)$flags['level']));
        }

        if (isset($flags['comment'])) {
            $this->comment = (string)$flags['comment'];
        }
    }

    /**
     * Ajoute un fichier à l'archive ZIP
     *
     * @param string $data Contenu du fichier
     * @param string $filename Nom du fichier dans l'archive
     * @param array $flags Options : 'time' pour définir la date du fichier
     */
    public function addFile(string $data, string $filename, array $flags = []): void
    {
        // Gestion du chemin selon storePath
        if ($this->storePath !== 1) {
            $filename = str_contains($filename, '/') ? substr($filename, strrpos($filename, '/') + 1) : $filename;
        } else {
            $filename = preg_replace('/^(\.{1,2}[\/\\\\])+/', '', $filename);
        }

        $timestamp = !empty($this->replaceTime) ? $this->replaceTime : ($flags['time'] ?? time());
        $mtime = getdate($timestamp);

        // Conversion en format DOS pour ZIP
        $dosTime = (
            $mtime['year'] - 1980) << 25 
            | $mtime['mon'] << 21 
            | $mtime['mday'] << 16 
            | $mtime['hours'] << 11 
            | $mtime['minutes'] << 5 
            | ($mtime['seconds'] >> 1
        );

        $mtimePacked = pack('V', $dosTime);

        $crc32 = crc32($data);
        $normalLength = strlen($data);

        $compressed = gzcompress($data, $this->level);

        if ($compressed === false) {
            $this->error('Erreur lors de la compression');
            return;
        }

        // Retire les en-têtes GZIP
        $compressed = substr($compressed, 2, -4);
        $compLength = strlen($compressed);

        // Création de l'en-tête local
        $localHeader = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00" . $mtimePacked .
            pack('VVVvv', $crc32, $compLength, $normalLength, strlen($filename), 0x00);

        $this->zipData[] = $localHeader . $filename . $compressed .
            pack('VVV', $crc32, $compLength, $normalLength);

        // Création de l'en-tête central
        $centralHeader = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00" . $mtimePacked .
            pack(
                'VVVvvvvvVV',
                $crc32,
                $compLength,
                $normalLength,
                strlen($filename),
                0x00,
                0x00,
                0x00,
                0x00,
                0x0000,
                $this->offset
            );

        $this->central[] = $centralHeader . $filename;
        $this->offset = strlen(implode('', $this->zipData));
    }

    /**
     * Ajoute plusieurs fichiers à l'archive
     *
     * @param array $fileList Liste des fichiers à ajouter
     */
    protected function addFiles(array $fileList): void
    {
        $pwd = getcwd();
        @chdir($this->cwd);

        foreach ($fileList as $current) {
            if (!@file_exists($current)) continue;

            $stat = stat($current);
            if ($stat === false) continue;

            $data = '';
            if ($stat[7] > 0) {
                $fp = @fopen($current, 'rb');
                if ($fp !== false) {
                    $data = fread($fp, $stat[7]) ?: '';
                    fclose($fp);
                }
            }

            $flags = ['time' => $stat[9]];
            $this->addFile($data, $current, $flags);
        }

        if ($pwd !== false) {
            @chdir($pwd);
        }
    }

    /**
     * Retourne les données binaires de l'archive ZIP
     *
     * @return string Données de l'archive
     */
    public function getArchiveData(): string
    {
        $central = implode('', $this->central);
        $zipData = implode('', $this->zipData);

        // En-tête de fin de répertoire central
        $endOfCentral = "\x50\x4b\x05\x06\x00\x00\x00\x00" .
            pack(
                'vvVVv',
                count($this->central),
                count($this->central),
                strlen($central),
                strlen($zipData),
                strlen($this->comment)
            ) . $this->comment;

        return $zipData . $central . $endOfCentral;
    }

    /**
     * Envoie le fichier ZIP au navigateur pour téléchargement
     *
     * @param string $filename Nom du fichier ZIP
     */
    public function fileDownload(string $filename): void
    {
        header('Content-Type: application/zip; name="'.$filename.'"');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $this->getArchiveData();
    }
}
