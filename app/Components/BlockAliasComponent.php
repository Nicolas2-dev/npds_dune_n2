<?php

use App\Support\Facades\Component;
use App\Library\Components\BaseComponent;

/*
<?= Component::blockAlias('R1'); ?>
<?= Component::blockAlias(['code' => 'L2']); ?>
<?= Component::blockAlias(['R3']); ?>
*/

class BlockAliasComponent extends BaseComponent
{
    /**
     * Alias de BlocIDComponent
     *
     * @param array|string $params Code du bloc : "R1", "L2" ou ['code' => 'R1']
     * @return string Contenu du bloc
     */
    public function render(array|string $params = []): string
    {
        return Component::blocID($params);
    }
}
