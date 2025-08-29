<?php

namespace App\Library\Language;


class Language
{

    /**
     * Retourne un code ISO de langue/pays selon les paramètres.
     *
     * @param int $l 1 pour inclure le code langue ISO 639-1, sinon 0
     * @param string $s Séparateur entre langue et pays (ex: '-', '_')
     * @param int $c 1 pour inclure le code pays ISO 3166-2, sinon 0
     * @return string Code formaté (ex: fr-FR, en, US, etc.)
     */
    public static function language_iso($l, $s, $c): string
    {
        global $language, $user_language;

        $iso_lang = '';
        $iso_country = '';
        $ietf = '';
        $select_lang = '';
        $select_lang = !empty($user_language) ? $user_language : $language;

        switch ($select_lang) {

            case 'french':
                $iso_lang = 'fr';
                $iso_country = 'FR';
                break;

            case 'english':
                $iso_lang = 'en';
                $iso_country = 'US';
                break;

            case 'spanish':
                $iso_lang = 'es';
                $iso_country = 'ES';
                break;

            case 'german':
                $iso_lang = 'de';
                $iso_country = 'DE';
                break;

            case 'chinese':
                $iso_lang = 'zh';
                $iso_country = 'CN';
                break;

            default:
                break;
        }

        if ($c !== 1) {
            $ietf = $iso_lang;
        }

        if (($l == 1) and ($c == 1)) {
            $ietf = $iso_lang . $s . $iso_country;
        }

        if (($l !== 1) and ($c == 1)) {
            $ietf = $iso_country;
        }

        if (($l !== 1) and ($c !== 1)) {
            $ietf = '';
        }

        if (($l == 1) and ($c !== 1)) {
            $ietf = $iso_lang;
        }

        return $ietf;
    }

    /**
     * Liste tous les dossiers de langues et les écrit dans storage/locale/language.php.
     *
     * @return string Liste des langues séparées par espace.
     */
    public static function language_list(): string
    {
        $local_path = '';
        $languageslist = '';

        if (isset($module_mark)) {
            $local_path = '../../';
        }

        $handle = opendir($local_path . 'language');

        while (false !== ($file = readdir($handle))) {
            if (!strstr($file, '.')) {
                $languageslist .= "$file ";
            }
        }

        closedir($handle);

        $file = fopen($local_path . 'storage/locale/language.php', 'w');

        fwrite($file, "<?php \$languageslist=\"" . trim($languageslist) . "\"; ?>");
        fclose($file);

        return $languageslist;
    }

    /**
     * Analyse le contenu d'une chaîne et convertit les sections correspondantes aux langues.
     * - [langue]...[/langue] affiche le contenu selon la langue courante.
     * - [!langue]...[/langue] masque le contenu si ce n'est pas la langue courante.
     * - [transl]...[/transl] simule un appel translate().
     *
     * @param string|null $ibid Chaîne à analyser et à transformer.
     * @return string Chaîne transformée avec les sections traduites.
     */
    public static function aff_langue(?string $ibid): string
    {
        global $language, $tab_langue;

        // copie du tableau + rajout de transl pour gestion de l'appel à translate(...); - Theme Dynamic
        $tab_llangue = $tab_langue;
        $tab_llangue[] = 'transl';

        reset($tab_llangue);

        $ok_language = false;
        $trouve_language = false;

        foreach ($tab_llangue as $key => $lang) {

            $pasfin = true;
            $pos_deb = false;
            $abs_pos_deb = false;
            $pos_fin = false;

            while ($pasfin) {

                // tags [langue] et [/langue]
                $pos_deb = strpos($ibid ?? '', "[$lang]", 0);
                $pos_fin = strpos($ibid ?? '', "[/$lang]", 0);

                if ($pos_deb === false) {
                    $pos_deb = -1;
                }

                if ($pos_fin === false) {
                    $pos_fin = -1;
                }

                // tags [!langue]
                $abs_pos_deb = strpos($ibid ?? '', "[!$lang]", 0);

                if ($abs_pos_deb !== false) {
                    $ibid = str_replace("[!$lang]", "[$lang]", $ibid);

                    $pos_deb = $abs_pos_deb;

                    if ($lang != $language) {
                        $trouve_language = true;
                    }
                }

                $decal = strlen($lang) + 2;

                if (($pos_deb >= 0) and ($pos_fin >= 0)) {
                    $fragment = substr($ibid, $pos_deb + $decal, ($pos_fin - $pos_deb - $decal));

                    if ($trouve_language == false) {
                        if ($lang != 'transl') {
                            $ibid = str_replace("[$lang]" . $fragment . "[/$lang]", $fragment, $ibid);
                        } else {
                            $ibid = str_replace("[$lang]" . $fragment . "[/$lang]", translate($fragment), $ibid);
                        }
                        $ok_language = true;
                    } else {
                        if ($lang != 'transl') {
                            $ibid = str_replace("[$lang]" . $fragment . "[/$lang]", "", $ibid);
                        } else {
                            $ibid = str_replace("[$lang]" . $fragment . "[/$lang]", translate($fragment), $ibid);
                        }
                    }
                } else {
                    $pasfin = false;
                }
            }

            if ($ok_language) {
                $trouve_language = true;
            }
        }

        return $ibid;
    }

    /**
     * Charge le tableau des langues disponibles.
     *
     * @return array<string> Tableau des langues.
     */
    public static function make_tab_langue(): array
    {
        global $language, $languageslist;

        $languageslocal = $language . ' ' . str_replace($language, '', $languageslist);
        $languageslocal = trim(str_replace('  ', ' ', $languageslocal));
        $tab_langue = explode(' ', $languageslocal);

        return $tab_langue;
    }

    /**
     * Génère une zone HTML de sélection de langue.
     *
     * @param string $ibid Nom du champ select.
     * @return string HTML du formulaire de sélection de langue.
     */
    public static function aff_localzone_langue(string $ibid): string
    {
        global $tab_langue;

        $flag = array('french' => '🇫🇷', 'spanish' => '🇪🇸', 'german' => '🇩🇪', 'english' => '🇺🇸', 'chinese' => '🇨🇳');

        $M_langue = '<div class="mb-3">
            <select name="' . $ibid . '" class="form-select" onchange="this.form.submit()" aria-label="' . translate('Choisir une langue') . '">
                <option value="">' . translate('Choisir une langue') . '</option>';

        foreach ($tab_langue as $bidon => $langue) {
            $M_langue .= '<option value="' . $langue . '">' . $flag[$langue] . ' ' . translate('$langue') . '</option>';
        }

        $M_langue .= '<option value="">- ' . translate('Aucune langue') . '</option>
            </select>
        </div>
        <noscript>
            <input class="btn btn-primary" type="submit" name="local_sub" value="' . translate('Valider') . '" />
        </noscript>';

        return $M_langue;
    }

    /**
     * Génère un formulaire complet de sélection de langue.
     *
     * @param string $ibid_index URL du formulaire. Si vide, utilise la page actuelle.
     * @param string $ibid Nom du champ de sélection.
     * @param string $mess Message à afficher avant la sélection.
     * @return string HTML du formulaire complet.
     */
    public static function aff_local_langue(string $ibid_index, string $ibid, string $mess = ''): string
    {
        if ($ibid_index == '') {
            global $REQUEST_URI;
            $ibid_index = $REQUEST_URI;
        }

        $M_langue = '<form action="' . $ibid_index . '" name="local_user_language" method="post">';

        $M_langue .= $mess . static::aff_localzone_langue($ibid);

        $M_langue .= '</form>';

        return $M_langue;
    }

    /**
     * Prévisualise une chaîne avec une langue temporaire.
     *
     * @param string|null $local_user_language Langue temporaire à utiliser.
     * @param string $ibid Chaîne à traduire.
     * @return string Chaîne traduite avec la langue temporaire.
     */
    public static function preview_local_langue(?string $local_user_language, string $ibid): string
    {
        if ($local_user_language) {

            global $language, $tab_langue;

            $old_langue = $language;
            $language = $local_user_language;

            $tab_langue = static::make_tab_langue();
            $ibid = static::aff_langue($ibid);

            $language = $old_langue;
        }

        return $ibid;
    }

}
