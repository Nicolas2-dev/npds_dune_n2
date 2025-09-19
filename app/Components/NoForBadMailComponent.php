<?php

namespace App\Components;

use Npds\Config\Config;
use App\Support\Facades\Forum;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::noForBadMail(); ?>
*/

class NoForBadMailComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $user, $cookie;

        $output = '';

        if (Config::get('user.subscribe') && $user) {
            if (Forum::isadmailuser($cookie[0]) === true) {
                $output = '!delete!';
            }
        }

        return $output;
    }
}
