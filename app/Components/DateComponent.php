<?php

namespace App\Components;

use IntlDateFormatter;
use Npds\Config\Config;
use App\Support\Facades\Language;
use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
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
        $formatter = datefmt_create(
                Language::languageIso(1, '_', 1),
                IntlDateFormatter::FULL,
                IntlDateFormatter::MEDIUM,
                Config::get('date.timezone') ?: date_default_timezone_get()
        );

        return $formatter ? datefmt_format($formatter, time()) : date('Y-m-d H:i:s');
    }
}
