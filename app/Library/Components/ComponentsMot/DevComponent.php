<?php

namespace App\Library\Components\ComponentsMot;

use App\Library\Components\BaseComponent;

// Composant "Dev" => affiche simplement le mot "Developpeur"
class DevComponent extends BaseComponent
{
    // <Component:Dev />
    public function render(array $props = []): string
    {
        return "Developpeur";
    }

}