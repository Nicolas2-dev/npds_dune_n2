<?php

namespace App\Library\Language;

use Npds\Config\Config;
use Npds\Support\Facades\Request;


class Language
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    
    /**
     * Retourne l'instance singleton du dispatcher.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * Récupère le cache des langues depuis un fichier ou génère la liste des langues.
     *
     * Si le fichier `storage/locale/language.php` existe, il est inclus et utilisé.
     * Sinon, la liste des langues est générée via `Language::languageList()`.
     *
     * @return string La liste des langues
     */
    public function languageCache(): string
    {
        if (file_exists('storage/locale/language.php')) {
            include 'storage/locale/language.php';
        } else {
            //include($local_path . 'manuels/list.php');
            $languageslist = Language::languageList();
        }

        return $languageslist;
    }

    /**
     * Retourne un code ISO de langue/pays selon les paramètres.
     *
     * @param int $l 1 pour inclure le code langue ISO 639-1, sinon 0
     * @param string $s Séparateur entre langue et pays (ex: '-', '_')
     * @param int $c 1 pour inclure le code pays ISO 3166-2, sinon 0
     * @return string Code formaté (ex: fr-FR, en, US, etc.)
     */
    public function languageIso($l, $s, $c): string
    {
        //global $language, $user_language; // global a revoir !

        $language       = Config::get('language.language');
        $user_language  = Config::get('language.user_language');

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
    public function languageList(): string
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
    public function affLangue(?string $ibid): string
    {
        global $tab_langue; // global a revoir !

        $language  = Config::get('language.language');

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
    public function makeTabLangue(): array
    {
        $language  = Config::get('language.language');

        $languageslist = $this->languageCache();

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
    public function affLocalzoneLangue(string $ibid): string
    {
        global $tab_langue; // global a revoir !

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
    public function affLocalLangue(string $ibid_index, string $ibid, string $mess = ''): string
    {
        if ($ibid_index == '') {
            $ibid_index = Request::uri();
        }

        $M_langue = '<form action="' . $ibid_index . '" name="local_user_language" method="post">';

        $M_langue .= $mess .  $this->affLocalzoneLangue($ibid);

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
    public function previewLocalLangue(?string $local_user_language, string $ibid): string
    {
        if ($local_user_language) {

            global $tab_langue; // global a revoir !

            $old_langue = Config::get('language.language');

            Config::set('language.language', $local_user_language);

            $tab_langue =  $this->makeTabLangue();
            $ibid       =  $this->affLangue($ibid);

            Config::set('language.language', $old_langue);
        }

        return $ibid;
    }
}
