<?php

namespace App\Components;

use App\Support\Facades\News;
use App\Support\Facades\Theme;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::articleCompletID(5); ?>      // article ID 5
    <?= Component::articleCompletID(0); ?>      // dernier article
    <?= Component::articleCompletID(-1); ?>     // avant-dernier article
*/

class ArticleCompletIDComponent extends BaseComponent
{
    /**
     * Rendu de l'article complet
     *
     * @param int $params ID de l'article
     * @return string HTML de l'article
     */
    public function render(array|int $params = []): string
    {
        $arg = is_array($params) ? ($params[0] ?? 0) : $params;

        if ($arg > 0) {
            $story_limit = 1;
            $news_tab = News::prepaAffNews("article", $arg, "");
        } else {
            $news_tab = News::prepaAffNews("index", "", "");
            $story_limit = abs($arg) + 1;
        }

        $aid       = unserialize($news_tab[$story_limit]['aid']);
        $informant = unserialize($news_tab[$story_limit]['informant']);
        $datetime  = unserialize($news_tab[$story_limit]['datetime']);
        $title     = unserialize($news_tab[$story_limit]['title']);
        $counter   = unserialize($news_tab[$story_limit]['counter']);
        $topic     = unserialize($news_tab[$story_limit]['topic']);
        $hometext  = unserialize($news_tab[$story_limit]['hometext']);
        $notes     = unserialize($news_tab[$story_limit]['notes']);
        $morelink  = unserialize($news_tab[$story_limit]['morelink']);
        $topicname = unserialize($news_tab[$story_limit]['topicname']);
        $topicimage= unserialize($news_tab[$story_limit]['topicimage']);
        $topictext = unserialize($news_tab[$story_limit]['topictext']);
        $s_id      = unserialize($news_tab[$story_limit]['id']);

        if ($aid) {
            ob_start();
            Theme::themeIndex(
                $aid, $informant, $datetime, $title, $counter,
                $topic, $hometext, $notes, $morelink,
                $topicname, $topicimage, $topictext, $s_id
            );
            $remp = ob_get_contents();
            ob_end_clean();
        } else {
            $remp = "";
        }

        return $remp;
    }
}
