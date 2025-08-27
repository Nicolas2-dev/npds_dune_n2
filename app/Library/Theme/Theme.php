<?php

namespace App\Library\Theme;


class Theme
{

    #autodoc theme_image($theme_img) : Retourne le chemin complet si l'image est trouvée dans le répertoire image du thème sinon false
    function theme_image($theme_img)
    {
        global $theme;

        if (@file_exists('themes/'. $theme .'/assets/'. $theme_img)) {
            return ('themes/'. $theme .'/assets/'. $theme_img);
        }

        return false;
    }
    
    function theme_list()
    {
        $handle = opendir('themes');

        while (false !== ($file = readdir($handle))) {
            if (($file[0] !== '_')
                and (!strstr($file, '.'))
                //and (!strstr($file, '__vierge'))
                and (!strstr($file, 'base'))
            ) {
                $themelist[] = $file;
            }
        }

        natcasesort($themelist);
        closedir($handle);

        return implode(' ', $themelist);
    }

}
