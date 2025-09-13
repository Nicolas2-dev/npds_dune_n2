<?php

namespace Npds\View\Contracts;


interface EngineInterface
{

    /**
     * Obtenez le contenu évalué de la vue.
     *
     * @param string $path Chemin vers le fichier de la vue
     * @param array  $data Données à injecter dans la vue
     * @return string Contenu rendu de la vue
     */
    public function get(string $path, array $data = []): string;

}
