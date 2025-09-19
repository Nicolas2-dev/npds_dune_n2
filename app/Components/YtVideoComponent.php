<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/* Exemple d'appel :
    <?= Component::YtVideo('dQw4w9WgXcQ'); ?>
*/

class YtVideoComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $id_yt_video = is_array($params) ? ($params[0] ?? '') : $params;
        $content = '';

        if(!defined('CITRON')) {
            $content .= '<div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/'.$id_yt_video.'" allowfullscreen="" frameborder="0"></iframe>
                         </div>';
        } else {
            $content .= '<div class="youtube_player" videoID="'.$id_yt_video.'"></div>';
        }

        return $content;
    }
}
