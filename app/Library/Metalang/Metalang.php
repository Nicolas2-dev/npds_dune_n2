<?php

namespace App\Library\Metalang;

use App\Library\Cache\SuperCacheEmpty;
use App\Library\Cache\SuperCacheManager;


class Metalang
{

    /**
     * Filtre un argument passé aux requêtes SQL.
     * Cette fonction est automatiquement appelée par META-LANG lors du passage de paramètres.
     *
     * @param string $arg L'argument à filtrer.
     * @return string L'argument filtré.
     */
    public static function argFilter(string $arg): string
    {
        return removeHack(stripslashes(htmlspecialchars(urldecode($arg), ENT_QUOTES, 'UTF-8')));
    }

    /**
     * Génère le code HTML pour un smiley ou une image.
     * Utilisée pour intégrer des smilies et comme service pour theme_img().
     *
     * @param string $ibid Nom ou identifiant de l'image.
     * @return string|false Code HTML de l'image ou false si l'image n'existe pas.
     */
    public static function MM_img(string $ibid): string|false
    {
        $ibid = static::argFilter($ibid);
        $ibidX = themeImage($ibid);

        if ($ibidX) {
            $ret = '<img src="' . $ibidX . '" alt="smiley" loading="lazy" />';
        } else {
            if (@file_exists('assets/images/' . $ibid)) {
                $ret = '<img src="assets/images/' . $ibid . '" alt="smiley" loading="lazy" />';
            } else {
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     * Appelle une fonction avec un nombre variable d'arguments.
     * Les arguments sont filtrés si c'est un tableau.
     *
     * @param callable $funct La fonction à exécuter.
     * @param array|mixed $arguments Les arguments à passer à la fonction.
     * @return mixed Le résultat de l'exécution de la fonction.
     */
    public static function charg(callable $funct, mixed $arguments): mixed
    {
        if (is_array($arguments)) {

            array_walk($arguments, 'argFilter');

            $nbr = count($arguments);

            switch ($nbr) {
                case 1:
                    $cmd = $funct($arguments[0]);
                    break;

                case 2:
                    $cmd = $funct($arguments[0], $arguments[1]);
                    break;

                case 3:
                    $cmd = $funct($arguments[0], $arguments[1], $arguments[2]);
                    break;

                case 4:
                    $cmd = $funct($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                    break;

                case 5:
                    $cmd = $funct($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
                    break;

                case 6:
                    $cmd = $funct($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
                    break;

                case 7:
                    $cmd = $funct($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6]);
                    break;

                case 8:
                    $cmd = $funct($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7]);
                    break;
            }
        } else {
            $cmd = $funct();
        }

        return $cmd;
    }

    public static function matchUri($racine, $R_uri)
    {
        $tab_uri = explode(' ', $R_uri);

        foreach ($tab_uri as $RR_uri) {
            if ($racine == $RR_uri) {
                return true;
            }
        }

        return false;
    }

    /**
     * Charge le glossaire des meta-langues depuis la base de données ou le cache.
     *
     * @global bool $SuperCache
     * @global array $CACHE_TIMINGS
     * @global string $REQUEST_URI
     * @global string $NPDS_Prefix
     *
     * @return array Glossaire des meta-langues
     */
    public static function chargMetalang(): array
    {
        global $SuperCache, $CACHE_TIMINGS, $REQUEST_URI, $NPDS_Prefix;

        if ($SuperCache) {
            $racine = parse_url(basename($REQUEST_URI));

            $cache_clef = '[metalang]==>' . $racine['path'] . '.common';
            $CACHE_TIMINGS[$cache_clef] = 86400;

            $cache_obj = new SuperCacheManager();
            $glossaire = $cache_obj->startCachingObjet($cache_clef);
        } else {
            $cache_obj = new SuperCacheEmpty();
        }

        if (($cache_obj->genereting_output == 1) || ($cache_obj->genereting_output == -1) || (!$SuperCache)) {

            settype($glossaire, 'array');

            $result = sql_query("SELECT def, content, type_meta, type_uri, uri 
                                FROM " . sql_prefix('metalang') . " 
                                WHERE type_meta='mot' 
                                OR type_meta='meta' 
                                OR type_meta='smil'");

            while (list($def, $content, $type_meta, $type_uri, $uri) = sql_fetch_row($result)) {

                // la syntaxe est presque la même que pour les blocs (on n'utilise que la racine de l'URI)
                // si type_uri="-" / uri site les URIs où les meta-mot NE seront PAS actifs (tous sauf ...)
                // si type_uri="+" / uri site les URI où les meta-mot seront actifs (seulement ...)
                // Le séparateur entre les URI est l'ESPACE
                // => Exemples : index.php user.php forum.php static.php

                if ($uri != '') {
                    $match = static::matchUri($racine['path'], $uri);

                    if (($match and $type_uri == '+') or (!$match and $type_uri == '-')) {
                        $glossaire[$def]['content'] = $content;
                        $glossaire[$def]['type'] = $type_meta;
                    }
                } else {
                    $glossaire[$def]['content'] = $content;
                    $glossaire[$def]['type'] = $type_meta;
                }
            }
        }

        if ($SuperCache) {
            $cache_obj->endCachingObjet($cache_clef, $glossaire);
        }

        return $glossaire;
    }

    /**
     * Analyse les arguments d'une fonction meta-lang.
     *
     * @param string $arg Chaîne d'arguments séparés par des virgules ou entre guillemets
     *
     * @return array Liste des arguments analysés
     */
    public static function anaArgs(string $arg): array
    {
        if (substr($arg, -1) == "\"") {
            $arguments[0] = str_replace("\"", '', $arg);
        } else {
            $arguments = explode(',', $arg);
        }

        return $arguments;
    }

    /**
     * Traite le contenu textuel pour remplacer les meta-langues.
     *
     * @param string $Xcontent Contenu texte à analyser et transformer
     *
     * @global array $meta_glossaire
     * @global bool $admin
     * @global bool $NPDS_debug
     * @global string $NPDS_debug_str
     * @global int $NPDS_debug_cycle
     *
     * @return string Contenu transformé avec les meta-langues appliquées
     */
    public static function metaLang(string $Xcontent): string
    {
        global $meta_glossaire, $admin, $NPDS_debug, $NPDS_debug_str, $NPDS_debug_cycle;

        // Reduction
        $Xcontent = str_replace('<!--meta', '', $Xcontent);
        $Xcontent = str_replace('meta-->', '', $Xcontent);
        $Xcontent = str_replace('!PHP!', '', $Xcontent);

        // Sauvegarde le contenu original / analyse et transformation
        $Ycontent = $Xcontent;
        $Xcontent = str_replace("\r", ' ', $Xcontent);
        $Xcontent = str_replace("\n", ' ', $Xcontent);
        $Xcontent = str_replace("\t", ' ', $Xcontent);
        $Xcontent = str_replace('<br />', ' ', $Xcontent);
        $Xcontent = str_replace('<BR />', ' ', $Xcontent);
        $Xcontent = str_replace('<BR>', ' ', $Xcontent);
        $Xcontent = str_replace('&nbsp;', ' ', $Xcontent);
        $Xcontent = strip_tags($Xcontent);

        if (trim($Xcontent)) {
            $Xcontent .= ' ';
            // for compatibility only with old dyna-theme !
            $Xcontent .= '!theme! ';
        } else {
            return $Ycontent;
        }

        $text = array_unique(explode(' ', $Xcontent));

        $Xcontent = $Ycontent;
        // Fin d'analyse / restauration du contenu original

        $tab = array();

        foreach ($text as $word) {
            // longueur minimale du mot : 2 semble un bon compromis sauf pour les smilies ... (1 est donc le choix par défaut)
            if (strlen($word) > 1) {
                $op = 0;
                $arguments = '';
                $cmd = '';

                $car_deb = substr($word, 0, 1);
                $car_fin = substr($word, -1);

                // entité HTML
                if ($car_deb != '&' and $car_fin != ';') {
                    // Mot 'pure'
                    if (($car_fin == '.' || $car_fin == ',' || $car_fin == ';' || $car_fin == '?' || $car_fin == ':') && ($word != '...')) {
                        $op = 1;
                        $Rword = substr($word, 0, -1);
                    }

                    // peut être une fonction
                    if ($car_fin == ')') {
                        $ibid = strpos($word, '(');

                        if ($ibid) {
                            $op = 2;
                            $Rword = substr($word, 0, $ibid);
                            $arg = substr($word, $ibid + 1, strlen($word) - ($ibid + 2));
                            $arguments = static::anaArgs($arg);
                        } else {
                            $op = 1;
                            $Rword = substr($word, 0, -1);
                        }
                    }

                    // peut être un mot encadré par deux balises
                    if (($car_deb == '[' && $car_fin == ']' && $word != '[code]') || ($car_deb == '{' && $car_fin == '}')) {
                        $op = 5;
                        $Rword = substr($word, 1, -1);
                    }
                } else {
                    $op = 9;
                    $Rword = $word;
                }

                if ($car_deb == '(' and $op != 2) {
                    $op = 3;
                    $Rword = substr($word, 1);
                }

                if ($op == 3 and $car_fin == ')') {
                    $op = 4;
                    $Rword = substr($Rword, 0, -1);
                }

                if ($op == 0) {
                    $Rword = $word;
                }

                // --- REMPLACEMENTS
                $type_meta = '';

                if (array_key_exists($Rword, $meta_glossaire)) {
                    $Cword = $meta_glossaire[$Rword]['content'];
                    $type_meta = $meta_glossaire[$Rword]['type'];
                } elseif (array_key_exists($Rword . $car_fin, $meta_glossaire)) {
                    $Cword = $meta_glossaire[$Rword . $car_fin]['content'];
                    $type_meta = $meta_glossaire[$Rword . $car_fin]['type'];

                    $Rword = $Rword . $car_fin;
                    $car_fin = '';
                } else {
                    $Cword = $Rword;
                }

                // Cword est un meta-mot ? (il en reste qui n'ont pas été interprétés par la passe du dessus ... ceux avec params !)
                if (substr($Cword, 0, 1) == '!') {
                    $car_meta = strpos($Cword, '!', 1);

                    if ($car_meta) {
                        $Rword = substr($Cword, 1, $car_meta - 1);
                        $arg = substr($Cword, $car_meta + 1);
                        $arguments = static::anaArgs($arg);

                        if (array_key_exists('!' . $Rword . '!', $meta_glossaire)) {
                            $Cword = $meta_glossaire['!' . $Rword . '!']['content'];
                            $type_meta = $meta_glossaire['!' . $Rword . '!']['type'];
                        } else {
                            $Cword = '';
                            $type_meta = '';
                        }
                    }
                }

                // Cword commence par $cmd ?
                if (substr($Cword, 0, 4) == "\$cmd") {
                    @eval($Cword);
                    if ($cmd === false) {
                        $Cword = "<span style=\"color: red; font-weight: bold;\" title=\"Meta-lang : bad return for function\">$Rword</span>";
                    } else {
                        $Cword = $cmd;
                    }
                }

                // Cword commence par function ?
                if ($Cword != '') {
                    if (substr($Cword, 0, 9) == 'function ') {
                        $Rword = 'MM_' . str_replace('!', '', $Rword);

                        if (!function_exists($Rword)) {
                            @eval($Cword);
                        }

                        $Cword = static::charg($Rword, $arguments);
                        $Rword = $word;
                    }
                }

                // si le mot se termine par ^ : on supprime ^ | cela permet d'assurer la protection d'un mot (intouchable)
                if ($car_fin == '^') {
                    $Cword = substr($Cword, 0, -1) . '&nbsp;';
                }

                // si c'est un meta : remplacement identique à str_replace
                if ($type_meta == 'meta') {
                    $tab[$Rword] = $Cword;
                } else {
                    if ($car_fin == substr($Rword, -1)) {
                        $car_fin = ' ';
                    }

                    $tab[$Rword . $car_fin] = $Cword . $car_fin;
                }

                if ($NPDS_debug and $admin) {
                    $NPDS_debug_str .= "=> $word<br />";
                }
            }
        }

        $Xcontent = strtr($Xcontent, $tab);

        // Avons-nous quelque chose à supprimer (balise !delete! .... !/!) ?
        while (strstr($Xcontent, '!delete!')) {
            $deb = strpos($Xcontent, '!delete!', 0);
            $fin = strpos($Xcontent, '!/!', $deb + 8);

            $Xcontent = ($fin)
                ? str_replace(substr($Xcontent, $deb, ($fin + 3) - $deb), '', $Xcontent)
                : str_replace('!delete!', '', $Xcontent);
        }

        $Xcontent = str_replace('!/!', '', $Xcontent);

        // traitement [code] ... [/code]
        if (strstr($Xcontent, '[code]')) {
            $Xcontent = affCode($Xcontent);
        }

        $NPDS_debug_cycle++;

        return $Xcontent;
    }
}
