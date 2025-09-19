<?php

use Npds\Config\Config;
use App\Support\Facades\Spam;
use App\Library\Components\BaseComponent;

/*
<?= Component::AdminMail(); ?>
*/

class AdminMailComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $email = Config::get('mailer.adminmail', '');

        return '<a href="mailto:' . Spam::antiSpam($email, 1) . '" target="_blank">' . Spam::antiSpam($email, 0) . '</a>';
    }
}
