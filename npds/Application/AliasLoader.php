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
     * @var array<string, string> [alias => classe originale]
     */
    protected static array $createdAliases = [];

    /**
     * 
     *
     * @return  void    [return description]
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
     * 
     *
     * @return  void    [return description]
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
