<?php

/**
 * Classe LanguageManager
 *
 * Fournit des méthodes utilitaires pour gérer les langues et leurs informations.
 * Implémente le pattern Singleton pour garantir une seule instance.
 */
final class LanguageManager
{

    /**
     * Instance unique du singleton
     * 
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * Tableau des langues supportées avec leurs informations
     * 
     * @var array<string, array{info:string, name:string, locale:string, dir:string}>
     */
    private array $langMap = [
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
    ];

    /**
     * Langue par défaut du système
     * 
     * @var string|null
     */
    private ?string $language = null;

    /**
     * Langue préférée de l'utilisateur
     * 
     * @var string|null
     */
    private ?string $user_language = null;

    /**
     * Langue actuellement sélectionnée
     * 
     * @var string|null
     */
    private ?string $selectedLang = null;


    /**
     * Constructeur privé pour implémentation Singleton
     */
    private function __construct() {}

    /**
     * Récupère l'instance unique de LanguageManager
     *
     * @return self
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Initialise les langues système et utilisateur
     *
     * @param string $language Langue par défaut du système
     * @param string $user_language Langue préférée de l'utilisateur
     */
    public function init(string $language, string $user_language): void
    {
        $this->language         = $language;
        $this->user_language    = $user_language;
    }

    /**
     * Ajoute une langue au tableau des langues.
     *
     * @param string $code Code de la langue (ex: 'fr', 'en', 'zh_TW').
     * @param array $info Tableau contenant les informations de la langue :
     *                    - 'info'   => Nom anglais complet
     *                    - 'name'   => Nom local
     *                    - 'locale' => Code locale (ex: 'fr_FR')
     *                    - 'dir'    => Direction du texte ('ltr' ou 'rtl')
     * 
     * @return void
     */
    public function addLanguage(string $code, array $info): void
    {
        $this->langMap[$code] = $info;
    }

    /**
     * Récupère l'ensemble des langues disponibles.
     *
     * @return array Tableau associatif des langues avec le code comme clé et les infos comme valeur.
     */
    public function getLanguages(): array
    {
        return $this->langMap;
    }

    /**
     * Récupère la langue actuellement sélectionnée
     *
     * @return string
     */
    private function getSelectedLang(): string
    {
        return $this->selectedLang ??= !empty($this->user_language) ? $this->user_language : $this->language;
    }

    /**
     * Récupère les informations complètes d'une langue
     *
     * @param string|null $code Code ISO ou nom de la langue
     * @return array|null Tableau avec 'info', 'name', 'locale', 'dir' ou null si non trouvé
     */
    private function getLangData(?string $code = null): ?array
    {
        $code ??= $this->getSelectedLang();

        $code = strtolower($code);

        if (isset($this->langMap[$code])) {
            return $this->langMap[$code];
        }

        foreach ($this->langMap as $lang) {
            if (stripos($lang['info'], $code) !== false || stripos($lang['name'], $code) !== false) {
                return $lang;
            }
        }

        return null;
    }

    /**
     * Récupère le code ISO de la langue
     *
     * @param string|null $code Code ISO ou nom de la langue
     * @return string|null Code ISO sur 2 lettres ou null
     */
    public function getIso(?string $code = null): ?string
    {
        $data = $this->getLangData($code);

        return $data ? substr($data['locale'], 0, 2) : null;
    }

    /**
     * Récupère le code du pays associé à la langue
     *
     * @param string|null $code Code ISO ou nom de la langue
     * @return string|null Code pays sur 2 lettres ou null
     */
    public function getCountry(?string $code = null): ?string
    {
        $data = $this->getLangData($code);

        return $data ? substr($data['locale'], 3, 2) : null;
    }

    /**
     * Récupère la locale complète de la langue
     *
     * @param string|null $code Code ISO ou nom de la langue
     * @param string $separator Séparateur à utiliser ('_' par défaut)
     * @return string|null Locale complète ou null
     */
    public function getFull(?string $code = null, string $separator = '_'): ?string
    {
        $data = $this->getLangData($code);

        return $data ? str_replace('_', $separator, $data['locale']) : null;
    }

    /**
     * Récupère le nom de la langue
     *
     * @param string|null $code Code ISO ou nom de la langue
     * @return string|null Nom de la langue ou null
     */
    public function getName(?string $code = null): ?string
    {
        $data = $this->getLangData($code);

        return $data['name'] ?? null;
    }

    /**
     * Récupère les informations générales de la langue
     *
     * @param string|null $code Code ISO ou nom de la langue
     * @return string|null Description de la langue ou null
     */
    public function getInfo(?string $code = null): ?string
    {
        $data = $this->getLangData($code);

        return $data['info'] ?? null;
    }

    /**
     * Récupère la direction d'écriture de la langue
     *
     * @param string|null $code Code ISO ou nom de la langue
     * @return string|null 'ltr' ou 'rtl'
     */
    public function getDirection(?string $code = null): ?string
    {
        $data = $this->getLangData($code);

        return $data['dir'] ?? null;
    }

    /**
     * Définit la langue système
     *
     * @param string $language Code ISO de la langue
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;

        $this->flush();
    }

    /**
     * Définit la langue préférée de l'utilisateur
     *
     * @param string $user_language Code ISO de la langue
     */
    public function setUserLanguage(string $user_language): void
    {
        $this->user_language = $user_language;

        $this->flush();
    }

    /**
     * Récupère la langue système
     *
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Récupère la langue préférée de l'utilisateur
     *
     * @return string|null
     */
    public function getUserLanguage(): ?string
    {
        return $this->user_language;
    }

    /**
     * Réinitialise la langue sélectionnée
     */
    public function flush(): void
    {
        $this->selectedLang = null;
    }

}
