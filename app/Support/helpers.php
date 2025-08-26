<?php

use App\Support\Compress\GzFile;
use App\Support\Compress\Archive;
use App\Support\Compress\ZipFile;


if (! function_exists('get_os')) {
    /**
     * Détecte si le système de l'utilisateur est Windows
     *
     * @return bool Retourne true si l'OS de la station cliente est Windows, sinon false
     */
    function get_os()
    {
        $client = getenv('HTTP_USER_AGENT');

        if (preg_match('#(\(|; )(Win)#', $client, $regs)) {
            if ($regs[2] == 'Win') {
                $MSos = true;
            } else {
                $MSos = false;
            }
        } else {
            $MSos = false;
        }

        return $MSos;
    }
}

if (! function_exists('send_file')) {
    /**
     * Compresse et télécharge un fichier
     *
     * @param string $line Contenu du fichier
     * @param string $filename Nom du fichier sans extension
     * @param string $extension Extension du fichier
     * @param bool $MSos Résultat de la fonction get_os()
     */
    function send_file($line, $filename, $extension, $MSos)
    {
        $compressed = is_compression_available();

        if ($compressed) {
            if ($MSos) {
                $arc = new ZipFile();
                $filez = $filename . '.zip';
            } else {
                $arc = new GzFile();
                $filez = $filename . '.gz';
            }

            $arc->addfile($line, $filename . '.' . $extension, '');
            $arc->getArchiveData();
            $arc->filedownload($filez);
        } else {
            if ($MSos) {
                header('Content-Type: application/octetstream');
            } else {
                header('Content-Type: application/octet-stream');
            }

            header('Content-Disposition: attachment; filename="' . $filename . ' . ' . $extension . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            echo $line;
        }
    }
}

if (! function_exists('send_to_file')) {
    /**
     * Compresse et enregistre un fichier dans un répertoire
     *
     * @param string $line Contenu du fichier
     * @param string $repertoire Répertoire de destination
     * @param string $filename Nom du fichier sans extension
     * @param string $extension Extension du fichier
     * @param bool $MSos Résultat de la fonction get_os()
     */
    function send_to_file($line, $repertoire, $filename, $extension, $MSos)
    {
        $compressed = is_compression_available();

        if ($compressed) {
            if ($MSos) {
                $arc = new ZipFile();
                $filez = $filename . '.zip';
            } else {
                $arc = new GzFile();
                $filez = $filename . '.gz';
            }

            $arc->addfile($line, $filename . '.' . $extension, '');
            $arc->getArchiveData();

            if (file_exists($repertoire . '/' . $filez)) {
                unlink($repertoire . '/' . $filez);
            }

            $arc->filewrite($repertoire . '/' . $filez, $perms = null);
        } else {
            if ($MSos) {
                header('Content-Type: application/octetstream');
            } else {
                header('Content-Type: application/octet-stream');
            }

            header("Content-Disposition: attachment; filename=\"$filename." . "$extension\"");
            header('Pragma: no-cache');
            header('Expires: 0');

            echo $line;
        }
    }
}

if (! function_exists('is_compression_available')) {
    /**
     * Vérifie si la compression via gzcompress est disponible
     *
     * Cette fonction teste si la classe Archive est chargée ou si le fichier
     * Archive.php existe, et si la fonction gzcompress est disponible.
     *
     * @return bool True si la compression est disponible, sinon false
     */
    function is_compression_available(): bool
    {
        if (class_exists(Archive::class) || file_exists('app/Support/Compress/Archive.php')) {
            if (function_exists('gzcompress')) {
                return true;
            }
        }

        return false;
    }
}
