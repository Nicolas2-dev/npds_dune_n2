<?php

namespace App\Components;

use App\Support\Facades\Spam;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::antiSpam('contact@npds.org'); ?>
    <?= Component::antiSpam(['email' => 'contact@npds.org']); ?>
    <?= Component::antiSpam(['contact@npds.org']); ?>
*/

class AntiSpamComponent extends BaseComponent
{
    /**
     * Rend un email protégé contre le spam
     *
     * @param array|string $params Email ou tableau ['email' => 'exemple@domaine.com'] ou [0 => 'email']
     * @return string HTML du lien mailto
     */
    public function render(array|string $params = []): string
    {
        // Déterminer l'email selon le type de param
        if (is_string($params)) {
            $email = $params;
        } elseif (is_array($params) && isset($params['email'])) {
            $email = $params['email'];
        } elseif (is_array($params) && isset($params[0])) {
            $email = $params[0];
        } else {
            return '';
        }

        // Appel à la fonction globale anti_spam
        return '<a href="mailto:' . Spam::antiSpam($email, 1) . '" target="_blank">'
               . Spam::antiSpam($email, 0) . '</a>';
    }
}
