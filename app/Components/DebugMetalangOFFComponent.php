<?php

use App\Library\Components\BaseComponent;

/*
@deprecated une foi metalang supprimer ! 
*/

/*
Exemples d'appel :
<?= Component::debugOFF(); ?>
*/
class DebugMetalangOFFComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $NPDS_debug, $NPDS_debug_str, $NPDS_debug_time, $NPDS_debug_cycle;

        $time_end = microtime(true);

        $NPDS_debug_str .= "=> !DebugOFF!<br /><b>=> exec time for meta-lang : "
            . round($time_end - $NPDS_debug_time, 4)
            . " / cycle(s) : $NPDS_debug_cycle</b><br />";
            
        $NPDS_debug = false;

        echo $NPDS_debug_str;

        return "";
    }
}
