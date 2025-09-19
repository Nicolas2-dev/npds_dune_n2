<?php

use App\Library\Components\BaseComponent;


// Note faire une lib Headlin + trait

/*
<?= Component::headlineID(3); ?>
*/

/**
 * Composant headlineID
 * [french]Récupération du canal RSS (ID) et fabrication d'un retour pour affichage[/french]
 */
class HeadlineIDComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $id = is_array($params) ? ($params[0] ?? '') : $params;

        // Appel natif à la fonction NPDS
        return @Headline::headlines($id, '');
    }
}
