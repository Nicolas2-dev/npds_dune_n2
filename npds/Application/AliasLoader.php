<?php

namespace Npds\Application;

use RuntimeException;
use Npds\Config\Config;

class AliasLoader
{

    /**
     * Initialise les alias de classes définis dans la configuration.
     *
     * Récupère les alias depuis la configuration 'kernel.aliases' et crée les alias
     * dans l’espace de noms global. Si une classe portant le même nom existe déjà,
     * une exception RuntimeException est levée.
     *
     * @return void
     *
     * @throws \RuntimeException Si un alias de classe existe déjà avec le même nom.
     */
    public static function initialize(): void
    {
        $classes = Config::get('kernel.aliases', array());

        foreach ($classes as $classAlias => $className) {
            // Garantit que l’alias est créé dans l’espace de noms global.
            $classAlias = '\\' .ltrim($classAlias, '\\');

            // Vérifie si la classe existe déjà.
            if (class_exists($classAlias)) {
                // Abandon : une classe existe déjà avec le même nom.
                throw new RuntimeException('Une classe [' .$classAlias .'] existe déjà avec le même nom.');
            }

            class_alias($className, $classAlias);
        }
    }

}
