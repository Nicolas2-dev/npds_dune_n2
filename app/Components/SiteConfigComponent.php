<?php

namespace App\Components;

use Npds\Config\Config;
use App\Support\Facades\Theme;
use App\Library\Components\BaseComponent;

/**
 * @deprecated
 */

/*
Exemple d'appel :
    <?= Component::siteConfig('slogan'); ?> <!-- Affiche Config::get('slogan') -->
    <?= Component::siteConfig('theme'); ?>  <!-- Affiche Config::get('theme') -->
    <?= Component::siteConfig('theme'); ?>  <!-- Affiche Theme::getTheme() -->
*/

class SiteConfigComponent extends BaseComponent
{
    /**
     * Rend la valeur d'un paramètre du site (slogan, thème, etc.)
     *
     * @param array|string $params Nom du paramètre ou tableau ['key' => 'slogan']
     * @return string
     */
    public function render(array|string $params = []): string
    {
        // Si un string est passé, on l'utilise comme clé
        $key = is_string($params) ? $params : ($params['key'] ?? '');

        if (!$key) {
            return '';
        }

        // Map des clés pour traduction en config
        $map = [
            'slogan'            => 'app.slogan',
            'theme'             => 'theme.Default_Theme',
            'current_theme'     => Theme::getTheme(),
            'sitename'          => 'app.sitename',
        ];

        if (!isset($map[strtolower($key)])) {
            return '';
        }

        // Récupération via Config::get()
        $value = Config::get($map[strtolower($key)], '');

        return (string) $value;
    }
}
