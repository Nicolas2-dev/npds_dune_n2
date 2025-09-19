<?php

use App\Library\Components\BaseComponent;

/**
 * Composant Note
 * [french]Permet de stocker une note en ligne qui ne sera jamais affichée !note! .... !/![/french]
 *
 * Exemple d'appel :
 *    <?= Component::note(); ?>
 */
class NoteComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        return "!delete!";
    }
}
