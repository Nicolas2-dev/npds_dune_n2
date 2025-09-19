<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
@deprecated une foi metalang supprimer ! 
*/

/*
Exemple d'appel :
    <?= Component::debugOn(); ?>
*/

class DebugMetalangOnComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $NPDS_debug, $NPDS_debug_str, $NPDS_debug_time, $NPDS_debug_cycle;

        $NPDS_debug_cycle = 1;
        $NPDS_debug       = true;
        $NPDS_debug_str   = "<br />";
        $NPDS_debug_time  = microtime(true);

        return "";
    }
}
