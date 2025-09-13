<?php

namespace Npds\Support\Contracts;


interface RenderableInterface
{
    
    /**
     * Obtenez le contenu évalué de l'objet.
     *
     * @return string
     */
    public function render();
}
