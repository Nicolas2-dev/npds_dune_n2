<?php

use App\Library\Components\BaseComponent;

/* Exemple d'appel :
<?= Component::TopicSubscribeOff(); ?>
*/
class TopicSubscribeOffComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        return '<div class="mb-3"><input type="hidden" name="op" value="maj_subscribe" />'
               .'<button class="btn btn-primary" type="submit" name="ok">'.translate("Valider").'</button>'
               .'</div></fieldset></form>';
    }
}
