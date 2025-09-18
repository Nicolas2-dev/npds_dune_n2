<?php

use Npds\Config\Config;
use App\Support\Facades\Theme;
use App\Support\Security\Hack;

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

if (!function_exists('theme_config')) {
    /**
     * Récupère une valeur de configuration spécifique au thème courant.
     *
     * @param string $key Clé de configuration relative au thème (ex: ".theme.name")
     * @param mixed  $default Valeur par défaut si la clé n'existe pas
     * @return mixed
     */
    function theme_config(string $key = '', mixed $default = null): mixed
    {
        $theme = Theme::getTheme();
        $configKey = 'theme_' . strtolower($theme) . ($key !== '' ? '.' . $key : '');

        if (Config::has($configKey)) {
            return Config::get($configKey, $default);
        }

        return $default;
    }
}

if (!function_exists('storage_path')) {
    /**
     * Retourne le chemin absolu vers le répertoire de stockage.
     *
     * Cette fonction concatène le chemin de base de stockage (`STORAGE_PATH`) 
     * avec un chemin optionnel fourni en argument. Utilise le séparateur de 
     * dossier défini par `DS`.
     *
     * @param string $path Chemin relatif à ajouter au répertoire de stockage (optionnel)
     *
     * @return string Chemin absolu complet vers le fichier ou dossier dans le stockage
     */
    function storage_path(string $path = ''): string
    {
        return STORAGE_PATH . DS . $path;
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

if (! function_exists('filemanager_config')) {
    /**
     * Retourne la configuration du filemanager
     *
     * @return mixed false|string
     */
    function filemanager_config()
    {
        $filemanager = false;

        if (Config::has('filemanager')) {
            $filemanager = Config::get('filemanager.filemanager');
        }

        return $filemanager;
    }
}

if (! function_exists('counterUpdate')) {
    /**
     * 
     *
     * @return  [type]  [return description]
     */
    function counterUpdate()
    {
        global $admin; // global a revoir !

        if ((!$admin) or (Config::get('user.not_admin_count') != 1)) {
            $user_agent = getenv('HTTP_USER_AGENT');

            if ((stristr($user_agent, 'Nav'))
                || (stristr($user_agent, 'Gold'))
                || (stristr($user_agent, 'X11'))
                || (stristr($user_agent, 'Mozilla'))
                || (stristr($user_agent, 'Netscape'))
                and (!stristr($user_agent, 'MSIE'))
                and (!stristr($user_agent, 'SAFARI'))
                and (!stristr($user_agent, 'IPHONE'))
                and (!stristr($user_agent, 'IPOD'))
                and (!stristr($user_agent, 'IPAD'))
                and (!stristr($user_agent, 'ANDROID'))
            ) {
                $browser = 'Netscape';
            } elseif (stristr($user_agent, 'MSIE')) {
                $browser = 'MSIE';
            } elseif (stristr($user_agent, 'Trident')) {
                $browser = 'MSIE';
            } elseif (stristr($user_agent, 'Lynx')) {
                $browser = 'Lynx';
            } elseif (stristr($user_agent, 'Opera')) {
                $browser = 'Opera';
            } elseif (stristr($user_agent, 'WebTV')) {
                $browser = 'WebTV';
            } elseif (stristr($user_agent, 'Konqueror')) {
                $browser = 'Konqueror';
            } elseif (stristr($user_agent, 'Chrome')) {
                $browser = 'Chrome';
            } elseif (stristr($user_agent, 'Safari')) {
                $browser = 'Safari';
            } elseif (preg_match('#([bB]ot|[sS]pider|[yY]ahoo)#', $user_agent)) {
                $browser = 'Bot';
            } else {
                $browser = 'Other';
            }

            if (stristr($user_agent, 'Win')) {
                $os = 'Windows';
            } elseif ((stristr($user_agent, 'Mac')) || (stristr($user_agent, 'PPC'))) {
                $os = 'Mac';
            } elseif (stristr($user_agent, 'Linux')) {
                $os = 'Linux';
            } elseif (stristr($user_agent, 'FreeBSD')) {
                $os = 'FreeBSD';
            } elseif (stristr($user_agent, 'SunOS')) {
                $os = 'SunOS';
            } elseif (stristr($user_agent, 'IRIX')) {
                $os = 'IRIX';
            } elseif (stristr($user_agent, 'BeOS')) {
                $os = 'BeOS';
            } elseif (stristr($user_agent, 'OS/2')) {
                $os = 'OS/2';
            } elseif (stristr($user_agent, 'AIX')) {
                $os = 'AIX';
            } else {
                $os = 'Other';
            }

            sql_query("UPDATE " . sql_prefix('counter') . " 
                    SET count=count+1 
                    WHERE (type='total' AND var='hits') 
                    OR (var='$browser' AND type='browser') 
                    OR (var='$os' AND type='os')");
        }
    }
}

if (! function_exists('refererUpdate')) {
    /**
     * 
     *
     * @return  [type]  [return description]
     */
    function refererUpdate()
    {
        if (Config::get('referer.httpref') == 1) {

            $referer = htmlentities(strip_tags(Hack::removeHack(getenv('HTTP_REFERER'))), ENT_QUOTES, 'UTF-8');

            if ($referer != '' and !strstr($referer, 'unknown') and !stristr($referer, $_SERVER['SERVER_NAME'])) {
                sql_query("INSERT INTO " . sql_prefix('referer') . " 
                        VALUES (NULL, '$referer')");
            }
        }
    }
}

if (! function_exists('file_contents_exist')) {   
    /**
     * Vérifie si une URL répond avec un code HTTP spécifique.
     *
     * Note : Fonction basique, peut ne pas fonctionner correctement avec HTTPS.
     *
     * @param string $url URL à vérifier
     * @param int $response_code Code HTTP attendu (par défaut 200)
     * @return bool Retourne true si l'URL renvoie le code attendu, false sinon
     */
    function file_contents_exist(string $url, int $response_code = 200): bool
    {
        $headers = get_headers($url);

        if (substr($headers[0], 9, 3) == $response_code) {
            return true;
        } else {
            return false;
        }
    }
}

if (! function_exists('data_image_to_file_url')) {
    /**
     * Convertit les images encodées en base64 dans une chaîne HTML
     * en fichiers physiques, puis remplace les balises `<img>` avec
     * le chemin du fichier généré.
     *
     * @param string $base64String Chaîne HTML contenant des images en base64.
     * @param string $outputPath   Chemin du dossier où enregistrer les images.
     * @return string              HTML modifié avec les URLs des fichiers images.
     *
     * @throws RuntimeException    Si une image est invalide ou non supportée.
     */
    function data_image_to_file_url(string $base64String, string $outputPath): string
    {
        $rechdataimage = '#src=\\\"(data:image/[^"]+)\\\"#m';

        preg_match_all($rechdataimage, $base64String, $dataimages);

        $j = 0;
        $timgw = 800;
        $timgh = 600;
        $ra = rand(1, 999);

        foreach ($dataimages[1] as $imagedata) {

            $datatodecode = explode(',', $imagedata);
            $bin = base64_decode($datatodecode[1]);
            $im = imageCreateFromString($bin);

            if (!$im) {
                die('Image non valide');
            }

            $size = getImageSizeFromString($bin);
            $ext = substr($size['mime'], 6);

            if (!in_array($ext, ['png', 'gif', 'jpeg'])) {
                die('Image non supportée');
            }

            $output_file = $outputPath . $j . '_' . $ra . '_' . time() . '.' . $ext;
            $base_64_string = preg_replace($rechdataimage, 'class="img-fluid" src="' . $output_file . '" loading="lazy"', $base64String, 1);

            if ($size[0] > $timgw or $size[1] > $timgh) {
                $timgh = round(($timgw / $size[0]) * $size[1]);
                $timgw = round(($timgh / $size[1]) * $size[0]);

                $th = imagecreatetruecolor($timgw, $timgh);
                imagecopyresampled($th, $im, 0, 0, 0, 0, $timgw, $timgh, $size[0], $size[1]);

                $args = [$th, $output_file];
            } else {
                $args = [$im, $output_file];
            }

            if ($ext == 'png') {
                $args[] = 0;
            } elseif ($ext == 'jpeg') {
                $args[] = 100;
            }

            $fonc = "image{$ext}";

            call_user_func_array($fonc, $args);

            $j++;
        }

        return site_url($base_64_string);
    }
}
