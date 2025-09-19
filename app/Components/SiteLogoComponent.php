<?php
use Npds\Config\Config;
use App\Library\Components\BaseComponent;

/*
<?= Component::SiteLogo(); ?>
*/

class SiteLogoComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $logo = Config::get('app.site_logo', '');

        return '<img src="' . $logo . '" border="0" alt="">';
    }
}
