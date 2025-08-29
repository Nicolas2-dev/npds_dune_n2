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

// SuperCache Function

if (! function_exists('q_select')) {
    /**
     * Exécute une requête SQL et renvoie le résultat, éventuellement via SuperCache.
     *
     * @param string $Xquery La requête SQL à exécuter.
     * @param int $retention Durée de rétention du cache en secondes (par défaut 3600).
     * @return array Tableau associatif des résultats.
     */
    function q_select(string $Xquery, int $retention = 3600): array
    {
        global $SuperCache, $cache_obj;

        if (($SuperCache) && ($cache_obj)) {
            $row = $cache_obj->CachingQuery($Xquery, $retention);

            return $row;
        } else {
            $result = @sql_query($Xquery);

            $tab_tmp = [];

            while ($row = sql_fetch_assoc($result)) {
                $tab_tmp[] = $row;
            }

            return $tab_tmp;
        }
    }
}

if (! function_exists('pg_clean')) {
    /**
     * Supprime le cache d'une page spécifique.
     *
     * @param string $request L'identifiant de la page à nettoyer.
     * @return void
     */
    function pg_clean(string $request): void
    {
        global $CACHE_CONFIG;

        $page = md5($request);

        $dh = opendir($CACHE_CONFIG['data_dir']);

        while (false !== ($filename = readdir($dh))) {
            if (
                $filename === '.'
                || $filename === '..'
                || (strpos($filename, $page) === false)
            ) {
                continue;
            }

            unlink($CACHE_CONFIG['data_dir'] . $filename);
        }

        closedir($dh);
    }
}

if (! function_exists('q_clean')) {
    /**
     * Nettoie tout le cache SQL.
     *
     * @return void
     */
    function q_clean(): void
    {
        global $CACHE_CONFIG;

        $dh = opendir($CACHE_CONFIG['data_dir'] . 'sql');

        while (false !== ($filename = readdir($dh))) {
            if ($filename === '.' or $filename === '..') {
                continue;
            }

            if (is_file($CACHE_CONFIG['data_dir'] . 'sql/' . $filename)) {
                unlink($CACHE_CONFIG['data_dir'] . 'sql/' . $filename);
            }
        }

        closedir($dh);

        $fp = fopen($CACHE_CONFIG['data_dir'] . 'sql/.htaccess', 'w');

        @fputs($fp, 'Deny from All');
        fclose($fp);
    }
}

if (! function_exists('sc_lean')) {
    /**
     * Nettoie tout le cache général, sauf fichiers de configuration spécifiques, puis nettoie le cache SQL.
     *
     * @return void
     */
    function sc_lean(): void
    {
        global $CACHE_CONFIG;

        $dh = opendir($CACHE_CONFIG['data_dir']);

        while (false !== ($filename = readdir($dh))) {
            if (
                $filename === '.'
                || $filename === '..'
                || $filename === 'ultramode.txt'
                || $filename === 'net2zone.txt'
                || $filename === 'sql' ||
                $filename === 'index.html'
            ) {
                continue;
            }

            if (is_file($CACHE_CONFIG['data_dir'] . $filename)) {
                unlink($CACHE_CONFIG['data_dir'] . $filename);
            }
        }

        closedir($dh);

        Q_Clean();
    }
}

if (! function_exists('sc_infos')) {
    /**
     * sc_infos() : Indique le statut de SuperCache
     */
    function sc_infos(): string
    {
        global $SuperCache, $npds_sc;

        $infos = '';

        if ($SuperCache) {
            /*
            $infos = $npds_sc ? '<span class="small">'.translate('.:Page >> Super-Cache:.").'</span>':'';
            */
            
            if ($npds_sc) {
                $infos = '<span class="small">' . translate('.:Page >> Super-Cache:.') . '</span>';
            } else {
                $infos = '<span class="small">' . translate('.:Page >> Super-Cache:.') . '</span>';
            }
        }

        return $infos;
    }
}
