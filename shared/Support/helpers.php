<?php

use Shared\FileSend\FileSender;

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

if (! function_exists('format_aid_header')) {
    /**
     * Affiche le lien URL ou Email d'un auteur à partir de son aid.
     *
     * Si l'auteur a une URL, le lien pointe vers celle-ci.
     * Sinon, si l'auteur a un email, le lien utilise "mailto:".
     * Sinon, affiche simplement l'identifiant.
     *
     * @param string $aid Identifiant de l'auteur.
     * @return void
     */
    function format_aid_header(string $aid): void
    {
        $holder = sql_query("SELECT url, email 
                            FROM " . sql_prefix('authors') . " 
                            WHERE aid='$aid'");

        if ($holder) {
            list($url, $email) = sql_fetch_row($holder);

            if (isset($url)) {
                echo '<a href="' . $url . '" >' . $aid . '</a>';
            } elseif (isset($email)) {
                echo '<a href="mailto:' . $email . '" >' . $aid . '</a>';
            } else {
                echo $aid;
            }
        }
    }
}
