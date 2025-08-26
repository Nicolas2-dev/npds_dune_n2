<?php

use App\Support\FileSender;


if (! function_exists('get_os')) {
    /**
     * Détecte si le système de l'utilisateur est Windows
     *
     * @return bool Retourne true si l'OS de la station cliente est Windows, sinon false
     */
    function get_os(): bool
    {
        $client = getenv('HTTP_USER_AGENT') ?: '';

        return preg_match('#(\(|; )Win#i', $client) === 1;
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
    function send_file(string $line, string $filename, string $extension, bool $MSos): void
    {
        FileSender::sendFile($line, $filename, $extension, $MSos);
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
    function send_to_file(string $line, string $repertoire, string $filename, string $extension, bool $MSos): void
    {
        FileSender::sendToFile($line, $repertoire, $filename, $extension, $MSos);
    }
}
