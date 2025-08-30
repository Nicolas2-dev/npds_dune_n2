<?php

namespace Npds\Container;

use Closure;
use ReflectionNamedType;

/**
 * @internal
 */
class Util
{

    /**
     * Si la valeur fournie n’est ni un tableau ni nulle, la mettre dans un tableau.
     *
     * Depuis Arr::wrap() dans Npds\Support
     *
     * @param  mixed  $value
     * @return array
     */
    public static function arrayWrap($value)
    {
        // Si la valeur est nulle, on retourne un tableau vide
        if (is_null($value)) {
            return [];
        }

        // Si la valeur est déjà un tableau, on la retourne telle quelle
        // Sinon, on la place dans un tableau (on "l'encapsule")
        return is_array($value) ? $value : [$value];
    }

    /**
     * Retourne la valeur par défaut de la valeur donnée.
     *
     * D’après le helper global value() dans Npds\Support.
     *
     * @param  mixed  $value
     * @param  mixed  ...$args
     * @return mixed
     */
    public static function unwrapIfClosure($value, ...$args)
    {
        // Vérifie si $value est une Closure (fonction anonyme)
        // Si oui, on l'exécute en lui passant les arguments $args et on retourne le résultat
        // Sinon, on retourne simplement $value tel quel
        return $value instanceof Closure ? $value(...$args) : $value;
    }

    /**
     * Obtenir le nom de la classe du type du paramètre donné, si possible.
     *
     * D’après Reflector::getParameterClassName() dans Npds\Support.
     *
     * @param  \ReflectionParameter  $parameter
     * @return string|null
     */
    public static function getParameterClassName($parameter)
    {
        // Récupère le type du paramètre passé à la fonction (ex : int, string, ou une classe)
        $type = $parameter->getType();

        // Si le type n'est pas un ReflectionNamedType (type nommé) ou si c'est un type natif (int, string, bool, etc.)
        // on retourne null, car il n'y a pas de classe associée
        if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }

        // Récupère le nom du type (ex : 'DateTime', 'User', 'self', 'parent')
        $name = $type->getName();

        // Vérifie si le paramètre appartient à une classe (méthode d'une classe)
        if (! is_null($class = $parameter->getDeclaringClass())) {
            
            // Si le type est 'self', cela fait référence à la classe dans laquelle la méthode est déclarée
            if ($name === 'self') {
                return $class->getName(); // retourne le nom complet de la classe
            }

            // Si le type est 'parent', on récupère le nom de la classe parente
            if ($name === 'parent' && $parent = $class->getParentClass()) {
                return $parent->getName(); // retourne le nom de la classe parente
            }
        }

        // Sinon, retourne simplement le nom du type (le nom de la classe)
        return $name;
    }

}
