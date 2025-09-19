<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::ScInfo(); ?> '.:Page >> Super-Cache:.'
*/

class ScInfoComponent extends BaseComponent
{

    /**
     * Rend les informations de Super-Cache.
     *
     * @param array $params Paramètres optionnels (non utilisés ici)
     *
     * @return string HTML des infos Super-Cache
     */
    public function render(array $params = []): string
    {
        global $SuperCache, $npds_sc;

        $infos = '';

        if ($SuperCache) {
            if ($npds_sc) {
                $infos = '<span class="small">' . translate('.:Page >> Super-Cache:.') . '</span>';
            } else {
                $infos = '<span class="small">' . translate('.:Page >> Super-Cache:.') . '</span>';
            }
        }

        return $infos;
    }

}
