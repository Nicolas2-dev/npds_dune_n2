<?php

include_once 'cache.config.php';
include_once 'cache.timings.php';

// Ces fonctions sont en dehors de la Classe pour permettre un appel sans instanciation d'objet
function Q_Select($Xquery, $retention = 3600)
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

function PG_clean($request)
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

function Q_Clean()
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

function SC_clean()
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
