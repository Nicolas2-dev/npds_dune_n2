<?php

namespace App\Components;

use Npds\Config\Config;
use App\Support\Facades\Forum;
use App\Library\Components\BaseComponent;

/*
Exemples d'appel :
    <?= Component::forumBoutonSubscribe(); ?>
*/

class ForumBoutonSubscribeComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $user;

        if (Config::get('user.subscribe') && $user) {
            $userX = base64_decode($user);
            $userR = explode(':', $userX);

            if (Forum::isBadMailUser($userR[0]) === false) {
                return '<input class="btn btn-secondary" type="submit" name="Xsub" value="' . translate("OK") . '" />';
            }
        }

        return '';
    }
}
