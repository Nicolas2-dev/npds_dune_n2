<?php

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
<?= Component::administration(); ?>
*/
class AdministrationComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $admin;

        if ($admin) {
            return '<a href="admin.php">' . translate("Outils administrateur") . '</a>';
        }
        
        return '';
    }
}
