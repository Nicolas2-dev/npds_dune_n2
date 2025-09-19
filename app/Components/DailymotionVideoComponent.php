<?php

namespace App\Components;

use App\Support\Sanitize;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::dailymotionVideo('x7yzabc'); ?>
    <?= Component::dailymotionVideo(['id' => 'x7yzabc']); ?>
*/

class DailymotionVideoComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $id = is_array($params) ? ($params['id'] ?? '') : $params;
        if (!$id) return '';

        $id = Sanitize::argFilter($id);

        if (!defined('CITRON')) {
            return '
            <div class="ratio ratio-16x9">
                <iframe src="https://www.dailymotion.com/embed/video/' . $id . '" allowfullscreen="" frameborder="0"></iframe>
            </div>';
        } else {
            return '<div class="dailymotion_player" videoID="' . $id . '"></div>';
        }
    }
}
