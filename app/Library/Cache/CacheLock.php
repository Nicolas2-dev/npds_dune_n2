<?php

namespace App\Library\Cache;

/**
 * Enumération des types de verrouillage pour les opérations de cache.
 */
enum CacheLock: int
{

    /**
     * Verrou exclusif (ex: pour écrire dans un fichier ou cache)
     */
    case LOCK_EX = 2; 

    /**
     * Déverrouillage (libération du verrou)
     */
    case LOCK_UN = 3; 

}
