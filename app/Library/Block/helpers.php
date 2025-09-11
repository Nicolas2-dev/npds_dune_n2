<?php

use App\Support\Facades\Block;


// Block Functions.

if (! function_exists('leftBlocks')) {
    /**
     * Exécute l'affichage ou le traitement des blocs positionnés à gauche.
     *
     * Cette fonction est un helper pour gérer les blocs de la colonne gauche
     * avec des classes CSS supplémentaires si nécessaire.
     *
     * @param  string  $moreclass Classes CSS supplémentaires à appliquer aux blocs.
     *
     * @return void
     */
    function leftBlocks(string $moreclass): void
    {
        Block::leftBlocks($moreclass);
    }
}

if (! function_exists('rightBlocks')) {
    /**
     * Exécute l'affichage ou le traitement des blocs positionnés à droite.
     *
     * Cette fonction est un helper pour gérer les blocs de la colonne droite
     * avec des classes CSS supplémentaires si nécessaire.
     *
     * @param  string  $moreclass Classes CSS supplémentaires à appliquer aux blocs.
     *
     * @return void
     */
    function rightBlocks(string $moreclass): void
    {
        Block::rightBlocks($moreclass);
    }
}
