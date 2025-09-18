<?php

namespace Npds\Application;

use RuntimeException;
use Npds\Config\Config;
use App\Library\Theme\Theme;

class AliasLoader
{

    /**
     * Liste des alias créés
     *
     * @var array<string, string>
     */
    protected static array $createdAliases = [];

    /**
     * Initialise les alias de classes définis dans la configuration.
     *
     * Cette méthode :
     * - Récupère les alias de classes depuis `kernel.aliases`.
     * - Vérifie que l'alias n'existe pas déjà.
     * - Crée un alias de classe avec `class_alias`.
     * - Stocke les alias créés dans `self::$createdAliases`.
     * - Charge les alias spécifiques aux thèmes via `loadThemeAliases()`.
     *
     * @throws RuntimeException Si un alias de classe existe déjà.
     * 
     * @return void
     */
    public static function initialize(): void
    {
        $classes = Config::get('kernel.aliases', []);

        foreach ($classes as $classAlias => $className) {
            $classAlias = '\\' . ltrim($classAlias, '\\');

            if (class_exists($classAlias)) {
                throw new RuntimeException('Une classe [' . $classAlias . '] existe déjà avec le même nom.');
            }

            class_alias($className, $classAlias);
            self::$createdAliases[$classAlias] = $className;
        }

        self::loadThemeAliases();
    }

    /**
     * Charge dynamiquement les alias de classes pour tous les thèmes disponibles.
     *
     * Pour chaque thème :
     * - Récupère tous les fichiers PHP dans le dossier `Support/Facades`.
     * - Génère le nom de classe complet.
     * - Crée un alias de classe commençant par `Theme_`.
     * - Stocke les alias créés dans `self::$createdAliases`.
     *
     * Cela permet d'utiliser facilement les facades des thèmes via des alias.
     *
     * @return void
     */
    public static function loadThemeAliases(): void
    {
        $theme_lists = Theme::getInstance()->themeList();
        $themeArray = explode(' ', $theme_lists);

        foreach ($themeArray as $themeName) {
            $facadePath = theme_path($themeName . '/Support/Facades/*.php');

            foreach (glob($facadePath) as $file) {
                $className = pathinfo($file, PATHINFO_FILENAME);

                $fullClassName = "Themes\\$themeName\\Support\\Facades\\$className";
                $aliasName = '\Theme_' . $className;

                if (class_exists($fullClassName) && !class_exists($aliasName)) {
                    class_alias($fullClassName, $aliasName);
                    self::$createdAliases[$aliasName] = $fullClassName;
                }
            }
        }
    }

    /**
     * Retourne la liste complète des alias créés
     *
     * @return array<string,string> [alias => classe originale]
     */
    public static function dumpAliases(): array
    {
        return self::$createdAliases;
    }

}
