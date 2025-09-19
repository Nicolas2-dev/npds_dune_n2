<?php

use App\Support\Sanitize;
use App\Support\Facades\Theme;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
<?= Component::themeImg('forum/onglet.gif'); ?>
*/
class ThemeImgComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $image = is_array($params) ? ($params[0] ?? '') : $params;
        $image = Sanitize::argFilter($image);

        // Cherche d'abord dans le thème
        $path = Theme::themeImage($image);

        if ($path) {
            return '<img src="' . $path . '" alt="' . basename($image) . '" loading="lazy" />';
        }

        // Fallback vers assets/images/
        if (@file_exists('assets/images/' . $image)) {
            return '<img src="assets/images/' . $image . '" alt="' . basename($image) . '" loading="lazy" />';
        }

        // Si l'image n'existe pas, retour vide
        return '';
    }

}
