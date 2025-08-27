<?php

/**
 * Classe LanguageManager
 *
 * Fournit des méthodes utilitaires pour gérer les langues et leurs informations.
 */
final class LanguageManager
{

    /**
     * Tableau des langues supportées avec leurs informations
     * 
     * @var array<string, array{info:string, name:string, locale:string, dir:string}>
     */
    private static array $langMap = [
        'cs' => ['info' => 'Czech',     'name' => 'čeština',    'locale' => 'cs_CZ', 'dir' => 'ltr'],
        'da' => ['info' => 'Danish',    'name' => 'Dansk',      'locale' => 'da_DK', 'dir' => 'ltr'],
        'de' => ['info' => 'German',    'name' => 'Deutsch',    'locale' => 'de_DE', 'dir' => 'ltr'],
        'en' => ['info' => 'English',   'name' => 'English',    'locale' => 'en_US', 'dir' => 'ltr'],
        'es' => ['info' => 'Spanish',   'name' => 'Español',    'locale' => 'es_ES', 'dir' => 'ltr'],
        'fa' => ['info' => 'Persian',   'name' => 'پارسی',      'locale' => 'fa_IR', 'dir' => 'rtl'],
        'fr' => ['info' => 'French',    'name' => 'Français',   'locale' => 'fr_FR', 'dir' => 'ltr'],
        'it' => ['info' => 'Italian',   'name' => 'italiano',   'locale' => 'it_IT', 'dir' => 'ltr'],
        'ja' => ['info' => 'Japanesse', 'name' => '日本語',      'locale' => 'ja_JA', 'dir' => 'ltr'],
        'nl' => ['info' => 'Dutch',     'name' => 'Nederlands', 'locale' => 'nl_NL', 'dir' => 'ltr'],
        'pl' => ['info' => 'Polish',    'name' => 'polski',     'locale' => 'pl_PL', 'dir' => 'ltr'],
        'ro' => ['info' => 'Romanian',  'name' => 'Română',     'locale' => 'ro_RO', 'dir' => 'ltr'],
        'ru' => ['info' => 'Russian',   'name' => 'ру́сский',    'locale' => 'ru_RU', 'dir' => 'ltr'],
        'zh' => ['info' => 'Chinese',   'name' => '中國人',      'locale' => 'zh_CN', 'dir' => 'ltr'],
        'zh_TW' => ['info' => 'Chinese (Traditional)', 'name' => '中文', 'locale' => 'zh_TW', 'dir' => 'ltr'],
    ];

    /**
     * Langue par défaut du système
     * 
     * @var string|null
     */
    private static ?string $language = null;

    /**
     * Langue préférée de l'utilisateur
     * 
     * @var string|null
     */
    private static ?string $user_language = null;

    /**
     * Langue sélectionnée actuellement
     * 
     * @var string|null
     */
    private static ?string $selectedLang = null;

    /**
     * Initialise les langues
     *
     * @param string $language Langue par défaut du système
     * @param string $user_language Langue préférée de l'utilisateur
     * 
     * @return void
     */
    public static function init(string $language, string $user_language): void
    {
        self::$language         = $language;
        self::$user_language    = $user_language;
    }

    /**
     * Retourne la langue sélectionnée (priorité à la langue utilisateur)
     *
     * @return string
     */
    private static function getSelectedLang(): string
    {
        return self::$selectedLang ??= !empty(self::$user_language) ? self::$user_language : self::$language;
    }

    /**
     * Récupère les données de la langue à partir d'un code
     *
     * @param string|null $code Code de la langue (ex: 'fr', 'en_US')
     * @return array|null Tableau avec info, name, locale et dir
     */
    private static function getLangData(?string $code = null): ?array
    {
        $code ??= self::getSelectedLang();
        $code = strtolower($code);

        if (isset(self::$langMap[$code])) {
            return self::$langMap[$code];
        }

        foreach (self::$langMap as $lang) {
            if (stripos($lang['info'], $code) !== false || stripos($lang['name'], $code) !== false) {
                return $lang;
            }
        }

        return null;
    }

    /**
     * Récupère le code ISO de la langue
     *
     * @param string|null $code
     * @return string|null
     */
    public static function getIso(?string $code = null): ?string
    {
        $data = self::getLangData($code);

        return $data ? substr($data['locale'], 0, 2) : null;
    }

    /**
     * Récupère le code du pays de la langue
     *
     * @param string|null $code
     * @return string|null
     */
    public static function getCountry(?string $code = null): ?string
    {
        $data = self::getLangData($code);

        return $data ? substr($data['locale'], 3, 2) : null;
    }

    /**
     * Récupère le code complet de la langue
     *
     * @param string|null $code
     * @param string $separator Séparateur entre langue et pays
     * @return string|null
     */
    public static function getFull(?string $code = null, string $separator = '_'): ?string
    {
        $data = self::getLangData($code);

        return $data ? str_replace('_', $separator, $data['locale']) : null;
    }

    /**
     * Récupère le nom local de la langue
     *
     * @param string|null $code
     * @return string|null
     */
    public static function getName(?string $code = null): ?string
    {
        $data = self::getLangData($code);

        return $data['name'] ?? null;
    }

    /**
     * Récupère la description en anglais de la langue
     *
     * @param string|null $code
     * @return string|null
     */
    public static function getInfo(?string $code = null): ?string
    {
        $data = self::getLangData($code);

        return $data['info'] ?? null;
    }

    /**
     * Récupère la direction du texte de la langue
     *
     * @param string|null $code
     * @return string|null 'ltr' ou 'rtl'
     */
    public static function getDirection(?string $code = null): ?string
    {
        $data = self::getLangData($code);

        return $data['dir'] ?? null;
    }

    /**
     * Définit la langue du système
     *
     * @param string $language
     * @return void
     */
    public static function setLanguage(string $language): void
    {
        self::$language = $language;
        self::flush(); 
    }

    /**
     * Définit la langue de l'utilisateur
     *
     * @param string $user_language
     * @return void
     */
    public static function setUserLanguage(string $user_language): void
    {
        self::$user_language = $user_language;
        self::flush();
    }

    /**
     * Récupère la langue du système
     *
     * @return string|null
     */
    public static function getLanguage(): ?string
    {
        return self::$language;
    }

    /**
     * Récupère la langue préférée de l'utilisateur
     *
     * @return string|null
     */
    public static function getUserLanguage(): ?string
    {
        return self::$user_language;
    }

    /**
     * Réinitialise la langue sélectionnée
     *
     * @return void
     */
    public static function flush()
    {
        self::$selectedLang = null;
    }
}
