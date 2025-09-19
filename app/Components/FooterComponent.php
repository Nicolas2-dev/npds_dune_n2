<?php

use App\Library\Components\BaseComponent;

/*
<?= Component::footer(); ?>
*/

class FooterComponent extends BaseComponent
{

    /**
     * Rend le footer
     *
     * @param array $params Paramètres optionnels (non utilisés ici)
     *
     * @return string HTML du footer
     */
    public function render(array $params = []): string
    {
        $footer = '';
        
        foreach (['foot1', 'foot2', 'foot3', 'foot4'] as $key) {
            if ($content = $this->config("theme.footer.$key")) {
                $footer .= stripslashes($content) . "<br />";
            }
        }

        return rtrim($footer, "<br />");
    }

}
