<?php

/**
 * Classe pour la gestion des fichiers ZIP
 */
class ZipFile extends Archive
{
    protected string $cwd = './';

    protected string $comment = '';

    protected int $level = 9;

    protected int $offset = 0;

    protected int $recurseSd = 1;

    protected int $storePath = 1;

    protected int $replaceTime = 0;

    protected array $central = [];

    protected array $zipData = [];


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
     */
    public function addFile(string $data, string $filename, array $flags = []): void
    {
        if ($this->storePath !== 1) {
            $filename = str_contains($filename, '/') ? substr($filename, strrpos($filename, '/') + 1) : $filename;
        } else {
            $filename = preg_replace('/^(\.{1,2}[\/\\\\])+/', '', $filename);
        }

        $timestamp = !empty($this->replaceTime) ? $this->replaceTime : ($flags['time'] ?? time());
        $mtime = getdate($timestamp);

        $dosTime = ($mtime['year'] - 1980) << 25 | $mtime['mon'] << 21 | $mtime['mday'] << 16 |
            $mtime['hours'] << 11 | $mtime['minutes'] << 5 | ($mtime['seconds'] >> 1);

        $mtimePacked = pack('V', $dosTime);

        $crc32 = crc32($data);
        $normalLength = strlen($data);

        $compressed = gzcompress($data, $this->level);

        if ($compressed === false) {
            $this->error('Erreur lors de la compression');
            return;
        }

        // Retire les en-têtes GZIP (2 premiers octets) et la fin (4 derniers octets)
        $compressed = substr($compressed, 2, -4);
        $compLength = strlen($compressed);

        // En-tête de fichier local
        $localHeader = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00" . $mtimePacked .
            pack('VVVvv', $crc32, $compLength, $normalLength, strlen($filename), 0x00);

        $this->zipData[] = $localHeader . $filename . $compressed .
            pack('VVV', $crc32, $compLength, $normalLength);

        // En-tête central
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
     */
    protected function addFiles(array $fileList): void
    {
        $pwd = getcwd();
        @chdir($this->cwd);

        foreach ($fileList as $current) {
            if (!@file_exists($current)) {
                continue;
            }

            $stat = stat($current);
            if ($stat === false) {
                continue;
            }

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
     * Retourne les données de l'archive ZIP
     */
    protected function getArchiveData(): string
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
            ) .
            $this->comment;

        return $zipData . $central . $endOfCentral;
    }

    /**
     * Télécharge le fichier ZIP
     */
    public function fileDownload(string $filename): void
    {
        header("Content-Type: application/zip; name=\"$filename\"");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $this->getArchiveData();
    }
}
