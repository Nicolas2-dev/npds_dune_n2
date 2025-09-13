<?php

namespace Npds\View\Contracts;


interface CompilerInterface
{

    /**
     * Obtenez le chemin d'accès à la version compilée d'une vue.
     *
     * @param string $path Chemin vers la vue source
     * @return string Chemin complet vers la vue compilée
     */
    public function getCompiledPath(string $path): string;

    /**
     * Déterminez si la vue donnée a expiré et doit être recompilée.
     *
     * @param string $path Chemin vers la vue source
     * @return bool True si la vue est expirée, false sinon
     */
    public function isExpired(string $path): bool;

    /**
     * Compile la vue sur le chemin donné.
     *
     * @param string $path Chemin vers la vue source
     * @return void
     */
    public function compile(string $path): void;

}
