<?php
use Npds\Config\Config;
use App\Library\Components\BaseComponent;

/*
<?= Component::Banner(); ?>
*/

class BannerComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $hlpfile;

        if (Config::get('banner.banners') && !$hlpfile) {
            ob_start();
                include 'banners.php'; // Note a revoir faire un trais pour cette function ou alors un event listener 
                $output = ob_get_contents();
            ob_end_clean();
            
            return $output;
        }

        return '';
    }
}
