<?php

namespace App\Components;

use Npds\Config\Config;
use App\Library\Components\BaseComponent;

/*
Exemples d'appel :
    <?= Component::forumMessage(); ?>
*/

class ForumMessageComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $user;

        if (!$user) {
            return translate("Devenez membre et vous disposerez de fonctions spécifiques : abonnements, forums spéciaux (cachés, membres, ..), statut de lecture, ...");
        }

        if (Config::get('user.subscribe') && $user) {
            return translate("Cochez un forum et cliquez sur le bouton pour recevoir un Email lors d'une nouvelle soumission dans celui-ci.");
        }

        return "";
    }
}