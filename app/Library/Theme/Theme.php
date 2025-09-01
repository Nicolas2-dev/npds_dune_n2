<?php

namespace App\Library\Theme;


class Theme
{

    /**
     * Retourne le chemin complet de l'image si elle existe dans le répertoire du thème.
     *
     * @param string $theme_img Nom du fichier image
     * @return string|false Chemin complet si trouvé, sinon false
     */
    public static function image(string $theme_img): string|false
    {
        global $theme;

        if (@file_exists('themes/' . $theme . '/assets/' . $theme_img)) {
            return 'themes/' . $theme . '/assets/' . $theme_img;
        }

        return false;
    }

    /**
     * Alias de self::image() pour retrouver une image dans le thème actif.
     *
     * @param string $theme_img Nom du fichier image
     * @return string|false Chemin complet si trouvé, sinon false
     */
    public static function themeImage($theme_img)
    {
        return static::image($theme_img);
    }

    /**
     * Retourne la liste des thèmes disponibles dans le dossier 'themes'.
     *
     * Les dossiers commençant par "_" ou contenant "base" ou un "." sont ignorés.
     *
     * @return string Liste des thèmes séparés par un espace
     */
    public static function themeList(): string
    {
        $themelist = [];
        $handle = opendir('themes');

        if ($handle !== false) {
            while (false !== ($file = readdir($handle))) {
                if (($file[0] !== '_')
                    && (!strstr($file, '.'))
                    && (!strstr($file, 'base'))
                ) {
                    $themelist[] = $file;
                }
            }

            natcasesort($themelist);
            closedir($handle);
        }

        return implode(' ', $themelist);
    }
}
