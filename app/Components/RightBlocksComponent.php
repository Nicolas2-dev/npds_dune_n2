<?php

namespace App\Components;

use App\Support\Facades\Block;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::rightBlocks('R1'); ?>
    <?= Component::rightBlocks(['code' => 'R2']); ?>
    <?= Component::rightBlocks(['R3']); ?>
*/

class RightBlocksComponent extends BaseComponent
{
    /**
     * Rend les blocs de droite
     *
     * @param array|string $params Code ou paramètres pour rightblocks
     * @return string HTML généré
     */
    public function render(array|string $params = []): string
    {
        // Récupère le code depuis string ou tableau
        $arg = is_array($params) ? ($params['code'] ?? $params[0] ?? '') : $params;

        ob_start();
            Block::rightBlocks($arg);
            $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}
