<?php

use Npds\Config\Config;
use App\Support\Facades\Forum;
use App\Library\Components\BaseComponent;

/* Exemple d'appel :
<?= Component::TopicSubscribeOn(); ?>
*/
class TopicSubscribeOnComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $user, $cookie;

        if (Config::get('user.subscribe') && $user && Forum::isBadMailUser($cookie[0])===false) {
            return '<form action="topics.php" method="post"><fieldset>';
        }

        return ''; 
    }
}
