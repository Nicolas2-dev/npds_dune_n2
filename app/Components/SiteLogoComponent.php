<?php

namespace App\Components;

use Npds\Config\Config;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    ent::SiteLogo(); ?>
*/

class SiteLogoComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $logo = Config::get('app.site_logo', '');

        return '<img src="' . $logo . '" border="0" alt="">';
    }
}
