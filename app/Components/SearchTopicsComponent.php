<?php

use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;

/*
<?= Component::searchTopics(); ?>                       <!-- aucun topic présélectionné -->
<?= Component::searchTopics(['selected' => 3]); ?>      <!-- présélectionne le topic id 3 -->
*/

class SearchTopicsComponent extends BaseComponent
{
    /**
     * Rend un formulaire de sélection des sujets
     *
     * @param array $params Paramètres optionnels (ex. 'selected' => 5 pour présélectionner un topic)
     * @return string HTML du formulaire
     */
    public function render(array $params = []): string
    {
        $selectedTopic = $params['selected'] ?? '';

        $html = '<form action="search.php" method="post">';
        $html .= '<label class="col-form-label">' . translate("Sujets") . ' </label>';
        $html .= '<select class="form-select" name="topic" onChange="submit()">';
        $html .= '<option value="">' . translate("Tous les sujets") . '</option>';

        $rows = Q_select("SELECT topicid, topictext FROM ".sql_prefix('topics')." ORDER BY topictext", 86400);

        foreach ($rows as $row) {
            $topicId = $row['topicid'];

            $topicText = Language::affLangue($row['topictext']);

            $isSelected = ($topicId == $selectedTopic) ? ' selected' : '';

            $html .= '<option value="' . $topicId . '"' . $isSelected . '>' . $topicText . '</option>';
        }

        $html .= '</select></form>';

        return $html;
    }
}
