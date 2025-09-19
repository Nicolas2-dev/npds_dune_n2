<?php

namespace App\Components;

use App\Support\Sanitize;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::articleID(5); ?>
    <?= Component::articleID(['id' => 10]); ?>
    <?= Component::articleID([15]); ?>
*/

class ArticleIDComponent extends BaseComponent
{
    /**
     * Rend un lien vers un article par son ID
     *
     * @param array|string $params ID de l'article ou ['id' => SID]
     * @return string HTML du lien
     */
    public function render(array|string $params = []): string
    {
        // Récupération de l'ID depuis string ou tableau
        $sid = is_string($params) ? $params : ($params['id'] ?? $params[0] ?? null);
        if (!$sid) return '';

        $sid = Sanitize::argFilter($sid);

        $rowQ = Q_select("SELECT title FROM " . sql_prefix('stories') . " WHERE sid='$sid'", 3600);
        if (empty($rowQ)) return '';

        $title = $rowQ[0]['title'];

        return '<a href="' . site_url('article.php?sid=' . $sid) . '">' . $title . '</a>';
    }
}
