<?php

use App\Support\Sanitize;
use App\Support\Facades\News;
use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;


/* Exemple d'appel :
<?= Component::topStories(5); ?>
*/
class TopStoriesComponent extends BaseComponent
{
    public function render(array|string $params = []): string
    {
        $arg = is_array($params) ? ($params[0] ?? 0) : $params;

        $content = '';

        $xtab = News::newsAff("libre", "ORDER BY counter DESC LIMIT 0, ".($arg*2), 0, $arg*2);
        
        $story_limit = 0;

        while($story_limit < $arg && $story_limit < sizeof($xtab)) {
            list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter) = $xtab[$story_limit];
            
            $story_limit++;

            if($counter > 0) {
                $content .= '<li class="ms-4 my-1"><a href="article.php?sid='.$sid.'" >'.Language::affLangue($title).'</a>&nbsp;<span class="badge bg-secondary float-end">'
                            .Sanitize::wrh($counter).' '.translate("Fois").'</span></li>';
            }
        }

        return $content;
    }
}
