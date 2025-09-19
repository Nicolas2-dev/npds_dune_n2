<?php

use App\Library\Components\BaseComponent;

// Note a revoir pour fichier config du module comment !

/* Exemple d'appel :
<?= Component::commentSystem('edito', 1); ?>
*/
class CommentSystemComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        global $NPDS_Prefix, $anonpost, $moderate, $admin, $user;

        [$file_name, $topic] = is_array($params) ? $params : [$params[0], $params[1]];

        ob_start();
        // a revoir !!!!
        if(file_exists("modules/comments/$file_name.conf.php")) {
            include "modules/comments/$file_name.conf.php";
            include "modules/comments/comments.php";
        }
        $output = ob_get_contents();
        ob_end_clean();
        
        return $output;
    }
}
