<?php

use Npds\Config\Config;
use App\Support\Facades\Messenger;
use App\Library\Components\BaseComponent;

/*
<?= Component::member(); ?>
*/

class MemberComponent extends BaseComponent
{
    /**
     * Affiche les informations du membre connecté
     *
     * @param array|string $params Paramètres optionnels (non utilisés ici)
     * @return string HTML généré
     */
    public function render(array|string $params = []): string
    {
        global $cookie; // global a revoir pour la viré !

        $username = $cookie[1] ?? Config::get('user.anonymous');

        // Capture le contenu généré par Mess_Check_Mail
        ob_start();
            Messenger::MessCheckMail($username);
            $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}
