<?php

if (! function_exists('init_lang')) {
    /**
     * Initialise la langue système et la langue utilisateur.
     *
     * @param string $language Langue par défaut du système.
     * @param string $user_language Langue préférée de l'utilisateur.
     * @return void
     */
    function init_lang(string $language, string $user_language): void
    {
        LanguageManager::getInstance()->init($language, $user_language);
    }
}

if (! function_exists('add_language')) {
    /**
     * Ajoute une langue au LanguageManager.
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
    function add_language(string $code, array $info): void
    {
        LanguageManager::getInstance()->addLanguage($code, $info);
    }
}

if (! function_exists('get_languages')) {
    /**
     * Récupère l'ensemble des langues disponibles depuis le LanguageManager.
     *
     * @return array Tableau associatif des langues avec le code comme clé et les informations comme valeur.
     */
    function get_languages(): array
    {
        return LanguageManager::getInstance()->getLanguages();
    }
}

if (! function_exists('get_iso_lang')) {
    /**
     * Récupère le code ISO de la langue (ex: "fr").
     *
     * @param string|null $code Code langue spécifique ou null pour langue sélectionnée.
     * @return string|null Code ISO de la langue ou null si non trouvé.
     */
    function get_iso_lang(?string $code = null): ?string
    {
        return LanguageManager::getInstance()->getIso($code);
    }
}

if (! function_exists('get_country_lang')) {
    /**
     * Récupère le code du pays associé à la langue (ex: "FR").
     *
     * @param string|null $code Code langue spécifique ou null pour langue sélectionnée.
     * @return string|null Code pays ou null si non trouvé.
     */
    function get_country_lang(?string $code = null): ?string
    {
        return LanguageManager::getInstance()->getCountry($code);
    }
}

if (! function_exists('get_full_lang')) {
    /**
     * Récupère la langue complète (locale) avec séparateur personnalisé.
     *
     * @param string|null $code Code langue spécifique ou null pour langue sélectionnée.
     * @param string $separator Séparateur entre langue et pays (par défaut "_").
     * @return string|null Locale complète ou null si non trouvé.
     */
    function get_full_lang(?string $code = null, string $separator = '_'): ?string
    {
        return LanguageManager::getInstance()->getFull($code, $separator);
    }
}

if (! function_exists('get_name_lang')) {
    /**
     * Récupère le nom local de la langue.
     *
     * @param string|null $code Code langue spécifique ou null pour langue sélectionnée.
     * @return string|null Nom local de la langue ou null si non trouvé.
     */
    function get_name_lang(?string $code = null): ?string
    {
        return LanguageManager::getInstance()->getName($code);
    }
}

if (! function_exists('get_info_lang')) {
    /**
     * Récupère l'information descriptive de la langue (en anglais).
     *
     * @param string|null $code Code langue spécifique ou null pour langue sélectionnée.
     * @return string|null Information de la langue ou null si non trouvé.
     */
    function get_info_lang(?string $code = null): ?string
    {
        return LanguageManager::getInstance()->getInfo($code);
    }
}

if (! function_exists('get_direction_lang')) {
    /**
     * Récupère la direction du texte de la langue ("ltr" ou "rtl").
     *
     * @param string|null $code Code langue spécifique ou null pour langue sélectionnée.
     * @return string|null Direction du texte ou null si non trouvé.
     */
    function get_direction_lang(?string $code = null): ?string
    {
        return LanguageManager::getInstance()->getDirection($code);
    }
}

if (! function_exists('set_language')) {
    /**
     * Définit la langue système par défaut.
     *
     * @param string $language Nouvelle langue par défaut.
     * @return void
     */
    function set_language(string $language): void
    {
        LanguageManager::getInstance()->setLanguage($language);
    }
}

if (! function_exists('set_user_language')) {
    /**
     * Définit la langue préférée de l'utilisateur.
     *
     * @param string $user_language Nouvelle langue utilisateur.
     * @return void
     */
    function set_user_language(string $user_language): void
    {
        LanguageManager::getInstance()->setUserLanguage($user_language);
    }
}

if (! function_exists('get_language')) {
    /**
     * Récupère la langue système actuelle.
     *
     * @return string|null Langue système ou null si non définie.
     */
    function get_language(): ?string
    {
        return LanguageManager::getInstance()->getLanguage();
    }
}

if (! function_exists('get_user_language')) {
    /**
     * Récupère la langue préférée de l'utilisateur.
     *
     * @return string|null Langue utilisateur ou null si non définie.
     */
    function get_user_language(): ?string
    {
        return LanguageManager::getInstance()->getUserLanguage();
    }
}

if (! function_exists('flush_lang')) {
    /**
     * Réinitialise la langue sélectionnée et force le recalcul.
     *
     * @return void
     */
    function flush_lang(): void
    {
        LanguageManager::getInstance()->flush();
    }
}
