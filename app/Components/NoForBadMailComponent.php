<?php

use Npds\Config\Config;
use App\Support\Facades\Forum;
use App\Library\Components\BaseComponent;

/*
<?= Component::noForBadMail(); ?>
*/

/**
 * Composant noforbadmail
 * [french]Test si le membre est dans la liste des mails incorrects.
 * Syntaxe : noforbadmail() ... !/![/french]
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
