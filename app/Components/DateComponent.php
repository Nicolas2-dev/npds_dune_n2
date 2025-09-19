<?php

use IntlDateFormatter;
use App\Library\Components\BaseComponent;

/*
<?= Component::date(); ?>
*/

class DateComponent extends BaseComponent
{
    /**
     * Rend la date actuelle formatée
     *
     * @param array|string $params Tableau de paramètres optionnels ou string (non utilisé ici)
     * @return string Date formatée
     */
    public function render(array|string $params = []): string
    {
        // Utilisation d'IntlDateFormatter pour le format complet + medium
        return datefmt_format(
            datefmt_create(
                null, // locale par défaut
                IntlDateFormatter::FULL,
                IntlDateFormatter::MEDIUM
            ),
            time()
        );
    }
}
