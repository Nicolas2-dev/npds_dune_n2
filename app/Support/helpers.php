<?php

// Path functions.

if (!function_exists('app_path')) {
    /**
     * Get the path to the application folder with capitalized segments.
     *
     * @param string $path Optional subpath to append
     * @return string
     */
    function app_path(string $path = ''): string
    {
        $basePath = rtrim(APPPATH, DS);

        if ($path === '') {
            return $basePath;
        }

        // Remplace tous les types de slash par DIRECTORY_SEPARATOR
        $segments = preg_split('/[\/\\\\]+/', $path);

        // Met la première lettre de chaque segment en majuscule
        $segments = array_map(fn($segment) => ucfirst($segment), $segments);

        $normalized_path = implode(DS, $segments);

        return $basePath . DS . $normalized_path;
    }
}

if (!function_exists('module_path')) {
    /**
     * Get the path to the modules folder with capitalized segments.
     *
     * @param string $path Optional subpath to append
     * @return string
     */
    function module_path(string $path = ''): string
    {
        $basePath = rtrim(MODULE_PATH, DS);

        if ($path === '') {
            return $basePath;
        }

        $segments = preg_split('/[\/\\\\]+/', $path);
        $segments = array_map(fn($segment) => ucfirst($segment), $segments);
        $normalized_path = implode(DS, $segments);

        return $basePath . DS . $normalized_path;
    }
}

if (!function_exists('theme_path')) {
    /**
     * Get the path to the themes folder with capitalized segments.
     *
     * @param string $path Optional subpath to append
     * @return string
     */
    function theme_path(string $path = ''): string
    {
        $basePath = rtrim(THEME_PATH, DS);

        if ($path === '') {
            return $basePath;
        }

        $segments = preg_split('/[\/\\\\]+/', $path);
        $segments = array_map(fn($segment) => ucfirst($segment), $segments);
        $normalized_path = implode(DS, $segments);

        return $basePath . DS . $normalized_path;
    }
}


// SuperCache Functions.

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
            $row = $cache_obj->cachingQuery($Xquery, $retention);

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

if (! function_exists('sc_clean')) {
    /**
     * Nettoie tout le cache général, sauf fichiers de configuration spécifiques, puis nettoie le cache SQL.
     *
     * @return void
     */
    function sc_clean(): void
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
