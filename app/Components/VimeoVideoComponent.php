<?php

namespace App\Components;

use App\Support\Sanitize;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::vimeoVideo('123456789'); ?>
    <?= Component::vimeoVideo(['id' => '123456789']); ?>
*/

class VimeoVideoComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $id = is_array($params) ? ($params['id'] ?? '') : $params;
        if (!$id) return '';

        $id = Sanitize::argFilter($id);

        if (!defined('CITRON')) {
            return '
            <div class="ratio ratio-16x9">
                <iframe src="https://player.vimeo.com/video/' . $id . '" allowfullscreen="" frameborder="0"></iframe>
            </div>';
        } else {
            return '<div class="vimeo_player" videoID="' . $id . '"></div>';
        }
    }
}
