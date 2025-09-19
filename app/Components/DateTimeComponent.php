<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::dateTime('dateL'); ?>       <!-- 18/09/2025 -->
    <?= Component::dateTime('dateC'); ?>       <!-- 18/09/25 -->
    <?= Component::dateTime('heureC'); ?>      <!-- 14:30 -->
    <?= Component::dateTime('heureL'); ?>      <!-- 14:30:12 -->

    <!-- Ou avec format personnalisé -->
    <?= Component::dateTime(['format' => 'l, d F Y H:i']); ?>  <!-- jeudi, 18 septembre 2025 14:30 -->
*/

class DateTimeComponent extends BaseComponent
{

    /**
     * Rend une date ou heure selon le type ou le format passé
     *
     * @param array|string $params {
     *     @type string $type   Type prédéfini : 'dateL', 'dateC', 'heureC', 'heureL'
     *     @type string $format Format PHP personnalisé (optionnel, prioritaire sur type)
     * }
     *
     * @return string
     */
    public function render(array|string $params = []): string
    {
        // Si on reçoit juste un string
        if (is_string($params)) {
            $params = ['type' => $params];
        }

        // Format personnalisé prioritaire
        $format = $params['format'] ?? null;

        // Si pas de format, on utilise le type
        if (!$format) {
            switch (strtolower($params['type'] ?? 'dateL')) {
                case 'datec':
                    $format = 'd/m/y';
                    break;
                case 'datel':
                    $format = 'd/m/Y';
                    break;
                case 'heurec':
                    $format = 'H:i';
                    break;
                case 'heurel':
                    $format = 'H:i:s';
                    break;
                default:
                    $format = 'd/m/Y';
            }
        }

        return date($format);
    }
}
