<?php

namespace Npds\View\Engines;

use Npds\View\Contracts\EngineInterface;


class FileEngine implements EngineInterface
{

    /**
     * Obtenez le contenu évalué de la vue.
     *
     * @param string $path Chemin complet vers le fichier de vue.
     * @param array  $data Données à passer à la vue (non utilisé ici).
     * @return string Contenu brut du fichier.
     */
    public function get(string $path, array $data = []): string
    {
        return file_get_contents($path);
    }
    
}
