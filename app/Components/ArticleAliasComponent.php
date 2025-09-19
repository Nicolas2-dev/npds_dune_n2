<?php

use App\Support\Facades\Component;
use App\Library\Components\BaseComponent;

/*
<?= Component::articleAlias(5); ?>
<?= Component::articleAlias(['id' => 10]); ?>
<?= Component::articleAlias([15]); ?>
*/

/**
 * Alias pour ArticleIDComponent
 */
class ArticleAliasComponent extends BaseComponent
{
    /**
     * Rend un lien vers un article en utilisant ArticleIDComponent
     *
     * @param array|string $params ID ou ['id' => SID]
     * @return string HTML du lien
     */
    public function render(array|string $params = []): string
    {
        return Component::articleID($params);
    }
}
