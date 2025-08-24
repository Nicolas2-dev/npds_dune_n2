<?php

/**
 * Classe pour la gestion des fichiers GZIP
 */
class GzFile extends Archive
{
    private string $gzData = '';

    /**
     * Ajoute un fichier à l'archive GZIP
     */
    public function addFile(string $data, ?string $filename = null, ?string $comment = null): void
    {
        $flags = bindec('000' . (!empty($comment) ? '1' : '0') . (!empty($filename) ? '1' : '0') . '000');
        
        $this->gzData .= pack('C1C1C1C1VC1C1', 0x1f, 0x8b, 8, $flags, time(), 2, 0xFF);
        
        if (!empty($filename)) {
            $this->gzData .= $filename . "\0";
        }
        
        if (!empty($comment)) {
            $this->gzData .= $comment . "\0";
        }
        
        $compressed = gzdeflate($data);
        if ($compressed === false) {
            $this->error('Erreur lors de la compression des données');
            return;
        }
        
        $this->gzData .= $compressed;
        $this->gzData .= pack('VV', crc32($data), strlen($data));
    }

    /**
     * Extrait les données d'un fichier GZIP
     */
    public function extract(string $data): array|bool
    {
        if (strlen($data) < 10) {
            $this->error('Données trop courtes pour un fichier GZIP valide');
            return false;
        }

        $id = unpack('H2id1/H2id2', substr($data, 0, 2));
        if ($id['id1'] !== '1f' || $id['id2'] !== '8b') {
            $this->error('Données GZIP non valides');
            return false;
        }

        $temp = unpack('Cflags', substr($data, 3, 1));
        $flags = $temp['flags'];
        
        $hasName = ($flags & 0x8) !== 0;
        $hasComment = ($flags & 0x4) !== 0;
        
        $offset = 10;
        $filename = '';
        
        if ($hasName) {
            while ($offset < strlen($data)) {
                $char = substr($data, $offset, 1);
                $offset++;
                if ($char === "\0") {
                    break;
                }
                $filename .= $char;
            }
        }
        
        if (empty($filename)) {
            $filename = 'file';
        }

        $comment = '';
        if ($hasComment) {
            while ($offset < strlen($data)) {
                $char = substr($data, $offset, 1);
                $offset++;
                if ($char === "\0") {
                    break;
                }
                $comment .= $char;
            }
        }

        if (strlen($data) < 8) {
            $this->error('Fichier GZIP tronqué');
            return false;
        }

        $temp = unpack('Vcrc32/Visize', substr($data, -8));
        $crc32 = $temp['crc32'];
        $isize = $temp['isize'];

        $compressedData = substr($data, $offset, strlen($data) - 8 - $offset);
        $decompressed = gzinflate($compressedData);
        
        if ($decompressed === false) {
            $this->error('Erreur lors de la décompression');
            return false;
        }

        if (crc32($decompressed) !== $crc32) {
            $this->error('Erreur de contrôle CRC32');
            return false;
        }

        return [
            'filename' => $filename,
            'comment' => $comment,
            'size' => $isize,
            'data' => $decompressed
        ];
    }

    /**
     * Retourne les données de l'archive
     */
    public function getArchiveData(): string
    {
        return $this->gzData;
    }

    /**
     * Télécharge le fichier
     */
    public function fileDownload(string $filename): void
    {
        header("Content-Type: application/x-gzip; name=\"$filename\"");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $this->getArchiveData();
    }
}
