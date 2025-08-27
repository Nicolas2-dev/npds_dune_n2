<?php

namespace App\Library\Language;


class Language
{

    #autodoc language_iso($l,$s,$c) : renvoi le code language iso 639-1 et code pays ISO 3166-2 $l=> 0 ou 1(requis), $s (séparateur - | _) , $c=> 0 ou 1 (requis)
    function language_iso($l, $s, $c)
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

    function language_list()
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

    #autodoc aff_langue($ibid) : Analyse le contenu d'une chaine et converti la section correspondante ([langue] OU [!langue] ...[/langue]) &agrave; la langue / [transl] ... [/transl] permet de simuler un appel translate('xxxx')
    function aff_langue($ibid)
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

    #autodoc make_tab_langue() : Charge le tableau TAB_LANGUE qui est utilisé par les fonctions multi-langue
    function make_tab_langue()
    {
        global $language, $languageslist;

        $languageslocal = $language . ' ' . str_replace($language, '', $languageslist);
        $languageslocal = trim(str_replace('  ', ' ', $languageslocal));
        $tab_langue = explode(' ', $languageslocal);

        return $tab_langue;
    }

    #autodoc aff_localzone_langue($ibid) : Charge une zone de formulaire de selection de la langue
    function aff_localzone_langue($ibid)
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

    #autodoc aff_local_langue($ibid_index, $ibid, $mess) : Charge une FORM de selection de langue $ibid_index = URL de la Form, $ibid = nom du champ
    function aff_local_langue($ibid_index, $ibid, $mess = '')
    {
        if ($ibid_index == '') {
            global $REQUEST_URI;
            $ibid_index = $REQUEST_URI;
        }

        $M_langue = '<form action="' . $ibid_index . '" name="local_user_language" method="post">';

        $M_langue .= $mess . aff_localzone_langue($ibid);

        $M_langue .= '</form>';

        return $M_langue;
    }

    #autodoc preview_local_langue($local_user_language,$ibid) : appel la fonction aff_langue en modifiant temporairement la valeur de la langue
    function preview_local_langue($local_user_language, $ibid)
    {
        if ($local_user_language) {

            global $language, $tab_langue;

            $old_langue = $language;
            $language = $local_user_language;

            $tab_langue = make_tab_langue();
            $ibid = aff_langue($ibid);

            $language = $old_langue;
        }

        return $ibid;
    }

}
