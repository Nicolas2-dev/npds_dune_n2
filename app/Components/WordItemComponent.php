<?php

use App\Library\Components\BaseComponent;

/*
<?= Component::worditem([
    'text'  => 'NPDS',
    'url'   => 'http://www.npds.org',
    'title' => 'Site officiel NPDS',
    'class' => 'highlight'
]); ?>

<?= Component::worditem([
    'text' => 'Dev'
]); ?>
*/

class WordItemComponent extends BaseComponent
{
    /**
     * Rend un mot avec éventuellement un lien et des options
     *
     * @param array $params {
     *     @type string $text Le mot à afficher
     *     @type string|null $url Optionnel, l'URL du lien
     *     @type string|null $title Optionnel, title du lien
     *     @type string|null $class Optionnel, classe CSS
     * }
     *
     * @return string HTML généré
     */
    public function render(array $params = []): string
    {
        $text  = $params['text'] ?? '';
        $url   = $params['url'] ?? null;
        $title = $params['title'] ?? '';
        $class = $params['class'] ?? '';

        if ($url) {
            return '<a href="' . $this->escape($url) . '" title="' . $this->escape($title) . '" class="' . $this->escape($class) . '">' 
                   . $this->escape($text) 
                   . '</a>';
        }

        return '<span class="' . $this->escape($class) . '">' . $this->escape($text) . '</span>';
    }
}
