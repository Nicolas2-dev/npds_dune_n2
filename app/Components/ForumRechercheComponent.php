<?php

use App\Support\Facades\Forum;
use App\Library\Components\BaseComponent;

/*
Exemples d'appel :
<?= Component::forumRecherche(); ?>
*/
class ForumRechercheComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        return @Forum::searchBlock();
    }
}