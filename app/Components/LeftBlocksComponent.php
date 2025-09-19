<?php

use App\Support\Facades\Block;
use App\Library\Components\BaseComponent;

/*
<?= Component::leftBlocks('R1'); ?>
<?= Component::leftBlocks(['code' => 'L2']); ?>
<?= Component::leftBlocks(['L3']); ?>
*/

class LeftBlocksComponent extends BaseComponent
{
    /**
     * Rend les blocs de gauche
     *
     * @param array|string $params Code ou paramètres pour leftblocks
     * @return string HTML généré
     */
    public function render(array|string $params = []): string
    {
        // Si string, on peut extraire directement le code
        $arg = is_array($params) ? ($params['code'] ?? $params[0] ?? '') : $params;

        ob_start();
            Block::leftBlocks($arg);
            $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}
