<?php

include_once 'library/compress/Archive.php';
include_once 'library/compress/GzFile.php';
include_once 'library/compress/ZipFile.php';

#autodoc get_os() : retourne true si l'OS de la station cliente est Windows sinon false
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

#autodoc send_file($line,$filename,$extension,$MSos) : compresse et t&eacute;l&eacute;charge un fichier / $line : le flux, $filename et $extension le fichier, $MSos (voir fonction get_os)
function send_file($line, $filename, $extension, $MSos)
{
    $compressed = false;

    if (file_exists('library/compress/Archive.php')) {
        if (function_exists('gzcompress')) {
            $compressed = true;
        }
    }

    if ($compressed) {
        if ($MSos) {
            $arc = new zipfile();
            $filez = $filename . '.zip';
        } else {
            $arc = new gzfile();
            $filez = $filename . '.gz';
        }

        $arc->addfile($line, $filename . '.' . $extension, '');
        $arc->arc_getdata();
        $arc->filedownload($filez);
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

#autodoc send_tofile($line,$repertoire,$filename,$extension,$MSos) : compresse et enregistre un fichier / $line : le flux, $repertoire $filename et $extension le fichier, $MSos (voir fonction get_os)
function send_tofile($line, $repertoire, $filename, $extension, $MSos)
{
    $compressed = false;

    if (file_exists('library/compress/Archive.php')) {
        if (function_exists('gzcompress')) {
            $compressed = true;
        }
    }

    if ($compressed) {
        if ($MSos) {
            $arc = new zipfile();
            $filez = $filename . '.zip';
        } else {
            $arc = new gzfile();
            $filez = $filename . '.gz';
        }

        $arc->addfile($line, $filename . '.' . $extension, '');
        $arc->arc_getdata();

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
