<?php

use Npds\Config\Config;
use App\Support\Facades\Forum;
use App\Library\Components\BaseComponent;

/*
Exemples d'appel :
<?= Component::forumSubscribeON(); ?>
*/
class ForumSubscribeONComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $user;

        $ibid = "";
        if (Config::get('user.subscribe') && $user) {
            $userX = base64_decode($user);
            $userR = explode(':', $userX);

            if (Forum::isBadMailUser($userR[0]) === false) {
                $ibid = '<form action="forum.php" method="post">
                          <input type="hidden" name="op" value="maj_subscribe" />';
            }
        }

        return $ibid;
    }
}