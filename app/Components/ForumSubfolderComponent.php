<?php

use App\Support\Sanitize;
use App\Support\Facades\Forum;
use App\Library\Components\BaseComponent;

/*
Exemples d'appel :
<?= Component::forumSubfolder(5); ?>
*/
class ForumSubfolderComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $forum = is_array($params) ? ($params[0] ?? '') : $params;
        $forum = Sanitize::argFilter($forum);

        return Forum::subForumFolder($forum);
    }
}