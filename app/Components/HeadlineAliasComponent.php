<?php

use HeadlineIDComponent; // attention namespace pas bon !
use App\Library\Components\BaseComponent;

/*
<?= Component::headlineAlias(3); ?>
*/

/**
 * Composant headline
 * [french]Alias de headlineID[/french]
 */
class HeadlineComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $id = is_array($params) ? ($params[0] ?? '') : $params;

        // Appel direct du composant HeadlineIDComponent
        return (new HeadlineIDComponent())->render([$id]);
    }
}
