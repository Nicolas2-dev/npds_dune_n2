<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/* 
Exemple d'appel :
    <?= Component::commentSystem('edito', 1); ?>
*/

class CommentSystemComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        
        [$file_name, $topic] = is_array($params) ? $params : [$params[0], $params[1]];

        ob_start();

        if(file_exists($path = module_path('Comments/Config/' . $file_name . '.php'))) {
            global $moderate, $admin, $user;

            include $path;

            include module_path('Comments/Http/Controllers/Front/comments.php');
        }

        $output = ob_get_contents();
        ob_end_clean();
        
        return $output;
    }
}
