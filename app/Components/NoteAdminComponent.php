<?php

use App\Library\Components\BaseComponent;

/**
 * Composant NoteAdmin
 * [french]Permet de stocker une note en ligne qui ne sera affichée que pour les administrateurs !note_admin! .... !/![/french]
 *
 * Exemple d'appel :
 *    <?= Component::noteAdmin(); ?>
 */
class NoteAdminComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $admin;

        if (!$admin) {
            return "!delete!";
        } else {
            return "<b>nota</b> : ";
        }
    }
}
