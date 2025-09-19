<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::info('Dev'); ?> 
    <?= Component::info('Npds'); ?>
*/

class InfoComponent extends BaseComponent
{
    /**
     * Liste des infos
     *
     * @var array<string, string>
     */
    private array $infos = [
        'Dev'  => 'Developpeur',
        'Npds' => '<a href="http://www.npds.org" target="_blank" title="www.npds.org">NPDS</a>',
    ];

    /**
     * Rend l'information correspondant à la clé
     *
     * @param array|string $params Clé ou tableau avec 'key' => clé
     * @return string HTML ou texte
     */
    public function render(array|string $params = []): string
    {
        // Si c'est juste un string, on le transforme en tableau
        if (is_string($params)) {
            $params = ['key' => $params];
        }

        $key = $params['key'] ?? '';

        return $this->infos[$key] ?? '';
    }
}
