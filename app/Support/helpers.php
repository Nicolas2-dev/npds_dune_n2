<?php

// Path functions.

if (!function_exists('base_path')) {
    /**
     * Get the path to the application folder with capitalized segments.
     *
     * @param string $path Optional subpath to append
     * @return string
     */
    function base_path(string $path = ''): string
    {
        $basePath = rtrim(BASEPATH, DS);

        if ($path === '') {
            return $basePath;
        }

        return $basePath . DS . $path;
    }
}

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

        return $basePath . DS . normalize_path($path);
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

        return $basePath . DS . normalize_path($path);
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

        return $basePath . DS . normalize_path($path);
    }
}

if (! function_exists('normalize_path')) {
    /**
     * Normalise un chemin de fichier ou de dossier.
     *
     * - Remplace tous les slashs ("/" ou "\") par le DIRECTORY_SEPARATOR.
     * - Met la première lettre de chaque segment de chemin en majuscule.
     *
     * Exemples :
     *   normalize_path('library/spam');      // 'Library/Spam'
     *   normalize_path('\theme\dark_mode');  // 'Theme/Dark_mode'
     *
     * @param string $path Chemin relatif à normaliser
     * @return string Chemin normalisé avec DIRECTORY_SEPARATOR et segments capitalisés
     */
    function normalize_path(string $path): string {
        // Remplace tous les types de slash par DIRECTORY_SEPARATOR
        $segments = preg_split('/[\/\\\\]+/', $path);

        // Met la première lettre de chaque segment en majuscule
        $segments = array_map(fn($segment) => ucfirst($segment), $segments);

        return implode(DS, $segments);
    }
}
