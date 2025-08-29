<?php

namespace App\Library\Components\ComponentsMot;

use App\Library\Components\BaseComponent;


// Composant "NPDS" => affiche un lien vers le site NPDS
class NPDSComponent extends BaseComponent
{
    // <Component:NPDS />
    public function render(array $props = []): string
    {
        return '<a href="http://www.npds.org" target="_blank" title="www.npds.org">NPDS</a>';
    }

}
