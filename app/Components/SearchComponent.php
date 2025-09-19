<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::search(); ?> 
    <?= Component::search(['action' => 'recherche.php', 'name' => 'q', 'size' => 20]); ?>
*/

class SearchComponent extends BaseComponent
{
    /**
     * Rend un formulaire de recherche simple
     *
     * @param array $params Paramètres optionnels :
     *                      - 'action' : URL du formulaire (default: "search.php")
     *                      - 'name'   : nom du champ texte (default: "query")
     *                      - 'size'   : taille du champ texte (default: 10)
     * @return string HTML du formulaire
     */
    public function render(array $params = []): string
    {
        $action = $params['action'] ?? 'search.php';
        $name   = $params['name'] ?? 'query';
        $size   = $params['size'] ?? 10;

        $html = '<form action="' . $this->escape($action) . '" method="post">';
        $html .= '<label>' . translate("Recherche") . '</label>';
        $html .= '<input class="form-control" type="text" name="' . $this->escape($name) . '" size="' . intval($size) . '">';
        $html .= '</form>';

        return $html;
    }
}
